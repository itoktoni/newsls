<?php

namespace App\Http\Controllers\Concerns;

use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\DB;

/**
 * Seri harian per jenis untuk report tanggal (Kotor vs Bersih, Invoice).
 *
 * - dateList()     : daftar 'Y-m-d' dari start s/d end (CarbonPeriod).
 * - kotorSeries()  : transaksi KOTOR (scan RS) grup tanggal + jenis.
 * - bersihSeries() : detail BERSIH (report date) grup tanggal + jenis.
 *
 * Bentuk hasil: ['names' => [jenisId => nama], 'berat' => [jenisId => float],
 *   'qty' => [tanggal][jenisId] => int]. Agregat GROUP BY di SQL.
 */
trait BuildsDateSeries
{
    protected function dateList(string $start, string $end): array
    {
        $out = [];
        foreach (CarbonPeriod::create($start, $end) as $day) {
            $out[] = $day->format('Y-m-d');
        }

        return $out;
    }

    protected function kotorSeries(int $rsId, string $start, string $end): array
    {
        $rows = DB::table('transaksi')
            ->join('detail_linen', 'detail_linen.detail_rfid', '=', 'transaksi.transaksi_rfid')
            ->leftJoin('jenis_linen', 'jenis_linen.jenis_id', '=', 'detail_linen.detail_id_jenis')
            ->where('transaksi.transaksi_status', 'KOTOR')
            ->where('transaksi.transaksi_rs_scan', $rsId)
            ->whereDate('transaksi.transaksi_created_at', '>=', $start)
            ->whereDate('transaksi.transaksi_created_at', '<=', $end)
            ->groupBy(DB::raw('DATE(transaksi.transaksi_created_at)'), 'detail_linen.detail_id_jenis', 'jenis_linen.jenis_nama', 'jenis_linen.jenis_berat')
            ->selectRaw('DATE(transaksi.transaksi_created_at) as tgl, detail_linen.detail_id_jenis as jenis_id, jenis_linen.jenis_nama as jenis_nama, jenis_linen.jenis_berat as jenis_berat, COUNT(*) as qty')
            ->get();

        return $this->assembleSeries($rows);
    }

    /**
     * Seri barang masuk gudang per tanggal grouping, jenis linen, dan RS.
     *
     * Hanya transaksi yang sudah memiliki tanggal grouping yang dihitung;
     * tanggal dibuat transaksi tidak digunakan sebagai fallback.
     */
    protected function groupingSeries(int $rsId, string $start, string $end): array
    {
        $rows = DB::table('transaksi')
            ->join('detail_linen', 'detail_linen.detail_rfid', '=', 'transaksi.transaksi_rfid')
            ->leftJoin('jenis_linen', 'jenis_linen.jenis_id', '=', 'detail_linen.detail_id_jenis')
            ->where('transaksi.transaksi_rs_scan', $rsId)
            ->whereNotNull('transaksi.transaksi_grouping_date')
            ->whereDate('transaksi.transaksi_grouping_date', '>=', $start)
            ->whereDate('transaksi.transaksi_grouping_date', '<=', $end)
            ->groupBy('transaksi.transaksi_grouping_date', 'detail_linen.detail_id_jenis', 'jenis_linen.jenis_nama', 'jenis_linen.jenis_berat')
            ->selectRaw('transaksi.transaksi_grouping_date as tgl, detail_linen.detail_id_jenis as jenis_id, jenis_linen.jenis_nama as jenis_nama, jenis_linen.jenis_berat as jenis_berat, COUNT(*) as qty')
            ->get();

        return $this->assembleSeries($rows);
    }

    /**
     * Seri bersih per tanggal report — dari tabel `bersih` (kolom
     * bersih_report, status BERSIH), BUKAN detail_linen. Kotor tetap dari
     * transaksi_created_at (lihat kotorSeries()).
     *
     * Tabel bersih legacy berhenti di MAX(bersih_report); delivery aliran
     * baru (cetak) hanya updating detail_linen — keduanya digabung dengan
     * rentang tanggal yang tidak beririsan (disjoint) sehingga tidak dobel.
     */
    protected function bersihSeries(int $rsId, string $start, string $end): array
    {
        $legacy = DB::table('bersih')
            ->leftJoin('detail_linen', 'detail_linen.detail_rfid', '=', 'bersih.bersih_rfid')
            ->leftJoin('jenis_linen', 'jenis_linen.jenis_id', '=', 'detail_linen.detail_id_jenis')
            ->where('bersih.bersih_status', 'BERSIH')
            ->where('bersih.bersih_id_rs', $rsId)
            ->whereDate('bersih.bersih_report', '>=', $start)
            ->whereDate('bersih.bersih_report', '<=', $end)
            ->groupBy('bersih.bersih_report', 'detail_linen.detail_id_jenis', 'jenis_linen.jenis_nama', 'jenis_linen.jenis_berat')
            ->selectRaw('bersih.bersih_report as tgl, detail_linen.detail_id_jenis as jenis_id, jenis_linen.jenis_nama as jenis_nama, jenis_linen.jenis_berat as jenis_berat, COUNT(*) as qty')
            ->get();

        // Aliran baru: BERSIH yang report-nya di luar jangkauan tabel legacy.
        $legacyMax = DB::table('bersih')->max('bersih_report');
        $fresh = collect();
        if ($legacyMax && $end > $legacyMax) {
            $from = max($start, date('Y-m-d', strtotime($legacyMax.' +1 day')));
            $fresh = DB::table('detail_linen')
                ->leftJoin('jenis_linen', 'jenis_linen.jenis_id', '=', 'detail_linen.detail_id_jenis')
                ->where('detail_linen.detail_id_rs', $rsId)
                ->where('detail_linen.detail_status_linen', 'BERSIH')
                ->whereDate('detail_linen.detail_report', '>=', $from)
                ->whereDate('detail_linen.detail_report', '<=', $end)
                ->groupBy('detail_linen.detail_report', 'detail_linen.detail_id_jenis', 'jenis_linen.jenis_nama', 'jenis_linen.jenis_berat')
                ->selectRaw('detail_linen.detail_report as tgl, detail_linen.detail_id_jenis as jenis_id, jenis_linen.jenis_nama as jenis_nama, jenis_linen.jenis_berat as jenis_berat, COUNT(*) as qty')
                ->get();
        }

        return $this->assembleSeries($legacy->concat($fresh));
    }

    /**
     * Tipe cuci dominan (CUCI/RENTAL) per jenis pada periode bersih.
     *
     * @return array<string, string> [jenisId => 'CUCI'|'RENTAL'|...]
     */
    protected function cuciMode(int $rsId, string $start, string $end): array
    {
        $rows = DB::table('detail_linen')
            ->where('detail_linen.detail_id_rs', $rsId)
            ->where('detail_linen.detail_status_linen', 'BERSIH')
            ->whereDate('detail_linen.detail_report', '>=', $start)
            ->whereDate('detail_linen.detail_report', '<=', $end)
            ->groupBy('detail_linen.detail_id_jenis', 'detail_linen.detail_status_cuci')
            ->selectRaw('detail_linen.detail_id_jenis as jenis_id, detail_linen.detail_status_cuci as cuci, COUNT(*) as qty')
            ->get();

        $mode = [];
        foreach ($rows as $row) {
            $key = (string) ($row->jenis_id ?? 'null');
            if (! isset($mode[$key]) || $row->qty > $mode[$key]['qty']) {
                $mode[$key] = ['cuci' => $row->cuci ?? '-', 'qty' => (int) $row->qty];
            }
        }

        return array_map(fn ($m) => $m['cuci'], $mode);
    }

    private function assembleSeries($rows): array
    {
        $names = [];
        $berat = [];
        $qty = [];

        foreach ($rows as $row) {
            $jenisKey = (string) ($row->jenis_id ?? 'null');
            $tgl = is_string($row->tgl) ? substr($row->tgl, 0, 10) : $row->tgl->format('Y-m-d');

            $names[$jenisKey] = $row->jenis_nama ?? 'Tanpa Jenis';
            $berat[$jenisKey] = (float) ($row->jenis_berat ?? 0);
            $qty[$tgl][$jenisKey] = ($qty[$tgl][$jenisKey] ?? 0) + (int) $row->qty;
        }

        asort($names);

        return compact('names', 'berat', 'qty');
    }
}
