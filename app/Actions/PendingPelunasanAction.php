<?php

namespace App\Actions;

use App\Models\JenisLinen;
use App\Models\Rs;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

/**
 * Pelunasan pending FIFO (hutang laundry per lot tanggal).
 *
 * Hutang (lot) = transaksi kotor/retur/rewash/register per (RS, jenis,
 * status, tanggal). Bayar = baris bersih yang sudah delivery, RFID boleh
 * beda. Setiap pembayaran menghabiskan lot tertua dulu; hasilnya menjawab:
 * lot tanggal X sudah terbayar berapa dan sisa berapa, serta delivery mana
 * yang membayarnya.
 *
 * Murni fungsi dari data append-only (transaksi + bersih) — tanpa tabel
 * perantara, bisa diaudit ulang kapan pun.
 *
 * @return array{lots: array, payments: array, total_sisa: int}
 *   lots[] = {rs_id,rs_nama,jenis_id,jenis_nama,status,tanggal,jumlah,terbayar,sisa,lunas,cicilan}
 *   cicilan[] = {tanggal, code, qty, sisa_sebelum, sisa_setelah, lunas_setelah}
 *   (1 baris cicilan per delivery; sisa_sebelum = hutang sebelum cicilan ini)
 *   payments[] = {code,tanggal,rs_nama,jenis_nama,status,jumlah,alokasi,kelebihan}
 *   alokasi[] = {lot_tanggal, qty}
 */
class PendingPelunasanAction
{
    use AsAction;

    public const PAIRED = [
        'KOTOR' => 'BERSIH',
        'REJECT' => 'REJECT',
        'REWASH' => 'REWASH',
        'REGISTER' => 'REGISTER',
    ];

    /**
     * Ringkasan per jenis dari lot: total masuk, terbayar, dan sisa pending.
     *
     * @return array<int, array{jenis_nama:string,masuk:int,terbayar:int,pending:int}>
     */
    public static function summarize(array $lots): array
    {
        $out = [];
        foreach ($lots as $lot) {
            $k = $lot['jenis_nama'];
            $out[$k] ??= ['jenis_nama' => $lot['jenis_nama'], 'masuk' => 0, 'terbayar' => 0, 'pending' => 0];
            $out[$k]['masuk'] += $lot['jumlah'];
            $out[$k]['terbayar'] += $lot['terbayar'];
            $out[$k]['pending'] += $lot['sisa'];
        }

        return array_values($out);
    }

    public function handle(array $filter = []): array
    {
        $lots = DB::table('transaksi as t')
            ->leftJoin('detail_linen as d', 'd.detail_rfid', '=', 't.transaksi_rfid')
            ->whereIn('t.transaksi_status', array_keys(self::PAIRED))
            ->when(! empty($filter['rs_id']), fn ($q) => $q->where('t.transaksi_rs_scan', $filter['rs_id']))
            ->when(! empty($filter['start']), fn ($q) => $q->whereDate('t.transaksi_created_at', '>=', $filter['start']))
            ->when(! empty($filter['end']), fn ($q) => $q->whereDate('t.transaksi_created_at', '<=', $filter['end']))
            ->groupBy('t.transaksi_rs_scan', 'd.detail_id_jenis', 't.transaksi_status', DB::raw('DATE(t.transaksi_created_at)'))
            ->orderBy(DB::raw('DATE(t.transaksi_created_at)'))
            ->selectRaw('t.transaksi_rs_scan as rs_id, d.detail_id_jenis as jenis_id, t.transaksi_status as status, DATE(t.transaksi_created_at) as tanggal, COUNT(*) as jumlah')
            ->get();

        $pays = DB::table('bersih as b')
            ->leftJoin('detail_linen as d', 'd.detail_rfid', '=', 'b.bersih_rfid')
            ->whereNotNull('b.bersih_delivery')
            ->when(! empty($filter['rs_id']), fn ($q) => $q->where('b.bersih_id_rs', $filter['rs_id']))
            ->when(! empty($filter['start']), fn ($q) => $q->whereDate('b.bersih_updated_at', '>=', $filter['start']))
            ->groupBy('b.bersih_id_rs', 'd.detail_id_jenis', 'b.bersih_status', DB::raw('DATE(b.bersih_updated_at)'), 'b.bersih_delivery')
            ->orderBy(DB::raw('DATE(b.bersih_updated_at)'))
            ->selectRaw('b.bersih_id_rs as rs_id, d.detail_id_jenis as jenis_id, b.bersih_status as status, DATE(b.bersih_updated_at) as tanggal, b.bersih_delivery as code, COUNT(*) as jumlah')
            ->get();

        $rsNames = Rs::whereIn('rs_id', $lots->pluck('rs_id')->merge($pays->pluck('rs_id'))->unique()->all())->pluck('rs_nama', 'rs_id');
        $jenisIds = $lots->pluck('jenis_id')->merge($pays->pluck('jenis_id'))->filter()->unique()->all();
        $jenisNames = JenisLinen::whereIn('jenis_id', $jenisIds)->pluck('jenis_nama', 'jenis_id');

        // Antrian lot per (rs, jenis, status-IN), tertua dulu.
        $queues = [];
        foreach ($lots as $lot) {
            $queues[$lot->rs_id.'|'.$lot->jenis_id.'|'.$lot->status][] = [
                'rs_id' => (int) $lot->rs_id,
                'rs_nama' => $rsNames[$lot->rs_id] ?? '-',
                'jenis_id' => (int) ($lot->jenis_id ?? 0),
                'jenis_nama' => $lot->jenis_id ? ($jenisNames[$lot->jenis_id] ?? '-') : 'TANPA JENIS',
                'status' => $lot->status,
                'tanggal' => $lot->tanggal,
                'jumlah' => (int) $lot->jumlah,
                'terbayar' => 0,
                'cicilan' => [],
            ];
        }

        $reverse = array_flip(self::PAIRED);
        $payments = [];
        foreach ($pays as $pay) {
            $inStatus = $reverse[$pay->status] ?? null;
            $key = $pay->rs_id.'|'.$pay->jenis_id.'|'.$inStatus;
            $sisa = (int) $pay->jumlah;
            $alokasi = [];

            if ($inStatus && isset($queues[$key])) {
                foreach ($queues[$key] as &$lot) {
                    if ($sisa <= 0) {
                        break;
                    }
                    $hutang = $lot['jumlah'] - $lot['terbayar'];
                    if ($hutang <= 0) {
                        continue;
                    }
                    $take = min($hutang, $sisa);
                    $lot['terbayar'] += $take;
                    $sisaSesudah = $lot['jumlah'] - $lot['terbayar'];
                    $lot['cicilan'][] = [
                        'tanggal' => $pay->tanggal, 'code' => $pay->code, 'qty' => $take,
                        'sisa_sebelum' => $hutang, 'sisa_setelah' => $sisaSesudah,
                        'lunas_setelah' => $sisaSesudah <= 0,
                    ];
                    $sisa -= $take;
                    $alokasi[] = ['lot_tanggal' => $lot['tanggal'], 'qty' => $take];
                }
                unset($lot);
            }

            $payments[] = [
                'code' => $pay->code,
                'tanggal' => $pay->tanggal,
                'rs_nama' => $rsNames[$pay->rs_id] ?? '-',
                'jenis_nama' => $pay->jenis_id ? ($jenisNames[$pay->jenis_id] ?? '-') : 'TANPA JENIS',
                'status' => $pay->status,
                'jumlah' => (int) $pay->jumlah,
                'alokasi' => $alokasi,
                'kelebihan' => $sisa,
            ];
        }

        $flat = [];
        $totalSisa = 0;
        foreach ($queues as $queue) {
            foreach ($queue as $lot) {
                $sisa = $lot['jumlah'] - $lot['terbayar'];
                $totalSisa += $sisa;
                $flat[] = $lot + ['sisa' => $sisa, 'lunas' => $sisa <= 0];
            }
        }

        return ['lots' => $flat, 'payments' => $payments, 'total_sisa' => $totalSisa];
    }
}
