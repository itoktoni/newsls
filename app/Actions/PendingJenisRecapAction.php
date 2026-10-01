<?php

namespace App\Actions;

use App\Models\JenisLinen;
use App\Models\Rs;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

/**
 * Rekap pending agregat per jenis (hutang laundry).
 *
 * RFID tidak bisa di-tracking per potong (GROUP/FREE), jadi pending dihitung
 * sebagai selisih: MASUK (transaksi kotor/retur/rewash/register per jenis)
 * dikurangi KELUAR (baris bersih yang sudah terkirim/delivery per jenis).
 * Pelunasan boleh memakai RFID mana saja — yang penting jenisnya sama.
 *
 * Pasangan status IN → OUT: KOTOR→BERSIH, REJECT→REJECT, REWASH→REWASH,
 * REGISTER→REGISTER. Selisih negatif dijepit ke 0 (data lama tak seimbang).
 *
 * Filter: rs_id (RS pemindai/penerima), start/end = periode TANGGAL KOTOR.
 * Keluar selalu dihitung all-time supaya pembayaran kapan pun mengurangi hutang.
 *
 * @return Collection<int, array{rs_id:int,rs_nama:string,jenis_id:int,jenis_nama:string,status:string,masuk:int,keluar:int,pending:int}>
 */
class PendingJenisRecapAction
{
    use AsAction;

    public const PAIRED = [
        'KOTOR' => 'BERSIH',
        'REJECT' => 'REJECT',
        'REWASH' => 'REWASH',
        'REGISTER' => 'REGISTER',
    ];

    public function handle(array $filter = []): Collection
    {
        $masuk = DB::table('transaksi as t')
            ->leftJoin('detail_linen as d', 'd.detail_rfid', '=', 't.transaksi_rfid')
            ->whereIn('t.transaksi_status', array_keys(self::PAIRED))
            ->when(! empty($filter['rs_id']), fn ($q) => $q->where('t.transaksi_rs_scan', $filter['rs_id']))
            ->when(! empty($filter['start']), fn ($q) => $q->whereDate('t.transaksi_created_at', '>=', $filter['start']))
            ->when(! empty($filter['end']), fn ($q) => $q->whereDate('t.transaksi_created_at', '<=', $filter['end']))
            ->groupBy('t.transaksi_rs_scan', 'd.detail_id_jenis', 't.transaksi_status')
            ->selectRaw('t.transaksi_rs_scan as rs_id, d.detail_id_jenis as jenis_id, t.transaksi_status as status, COUNT(*) as masuk')
            ->get();

        $keluarRows = DB::table('bersih as b')
            ->leftJoin('detail_linen as d', 'd.detail_rfid', '=', 'b.bersih_rfid')
            ->whereNotNull('b.bersih_delivery')
            ->when(! empty($filter['rs_id']), fn ($q) => $q->where('b.bersih_id_rs', $filter['rs_id']))
            // Pembayaran selalu terjadi SETELAH kotornya, jadi keluar cukup
            // dihitung dari awal periode (tanpa ini = full-table scan).
            ->when(! empty($filter['start']), fn ($q) => $q->whereDate('b.bersih_updated_at', '>=', $filter['start']))
            ->groupBy('b.bersih_id_rs', 'd.detail_id_jenis', 'b.bersih_status')
            ->selectRaw('b.bersih_id_rs as rs_id, d.detail_id_jenis as jenis_id, b.bersih_status as status, COUNT(*) as keluar')
            ->get();

        $keluar = [];
        foreach ($keluarRows as $row) {
            $keluar[$row->rs_id.'|'.$row->jenis_id.'|'.$row->status] = (int) $row->keluar;
        }

        $rsNames = Rs::whereIn('rs_id', $masuk->pluck('rs_id')->unique()->all())->pluck('rs_nama', 'rs_id');
        $jenisNames = JenisLinen::whereIn('jenis_id', $masuk->pluck('jenis_id')->filter()->unique()->all())->pluck('jenis_nama', 'jenis_id');

        $recap = [];
        foreach ($masuk as $row) {
            $in = (int) $row->masuk;
            $out = $keluar[$row->rs_id.'|'.$row->jenis_id.'|'.self::PAIRED[$row->status]] ?? 0;
            $pending = max(0, $in - $out);

            if ($pending <= 0) {
                continue;
            }

            $recap[] = [
                'rs_id' => (int) $row->rs_id,
                'rs_nama' => $rsNames[$row->rs_id] ?? '-',
                'jenis_id' => (int) ($row->jenis_id ?? 0),
                'jenis_nama' => $row->jenis_id ? ($jenisNames[$row->jenis_id] ?? '-') : 'TANPA JENIS',
                'status' => $row->status,
                'masuk' => $in,
                'keluar' => $out,
                'pending' => $pending,
            ];
        }

        return collect($recap)->sortBy([['rs_nama', 'asc'], ['jenis_nama', 'asc'], ['status', 'asc']])->values();
    }
}
