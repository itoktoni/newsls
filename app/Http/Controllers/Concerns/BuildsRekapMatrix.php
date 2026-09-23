<?php

namespace App\Http\Controllers\Concerns;

use Illuminate\Support\Facades\DB;

/**
 * Matriks rekap bersama: baris = jenis linen, kolom = ruangan,
 * sel = qty, plus total pcs + kg per baris dan footer per kolom.
 * Kg = qty × jenis_berat.
 *
 * - matrixFromTransaksi() : untuk status transaksi (KOTOR/REJECT/REWASH) —
 *   sumber `transaksi` (1 baris = 1 scan RFID), tanggal = transaksi_created_at.
 * - matrixFromDetailBersih() : untuk BERSIH — sumber tabel `bersih`
 *   (kolom bersih_report, status BERSIH) + ekor aliran baru
 *   (detail BERSIH di luar jangkauan legacy, disjoint).
 *
 * Agregasi GROUP BY di SQL sehingga hasilnya kecil berapa pun datanya.
 *
 * @return array{locations: array, linens: array, matrix: array, rowTotal: array, rowKg: array, colTotal: array, grandQty: int, grandKg: float}
 */
trait BuildsRekapMatrix
{
    protected function matrixFromTransaksi(int $rsId, string $status, ?string $start, ?string $end): array
    {
        $rows = DB::table('transaksi')
            ->join('detail_linen', 'detail_linen.detail_rfid', '=', 'transaksi.transaksi_rfid')
            ->leftJoin('jenis_linen', 'jenis_linen.jenis_id', '=', 'detail_linen.detail_id_jenis')
            ->leftJoin('ruangan', 'ruangan.ruangan_id', '=', 'transaksi.transaksi_id_ruangan')
            ->where('transaksi.transaksi_status', $status)
            ->where('transaksi.transaksi_rs_scan', $rsId)
            ->when(! empty($start), fn ($q) => $q->whereDate('transaksi.transaksi_created_at', '>=', $start))
            ->when(! empty($end), fn ($q) => $q->whereDate('transaksi.transaksi_created_at', '<=', $end))
            ->groupBy('detail_linen.detail_id_jenis', 'jenis_linen.jenis_nama', 'jenis_linen.jenis_berat', 'transaksi.transaksi_id_ruangan', 'ruangan.ruangan_nama')
            ->select([
                'detail_linen.detail_id_jenis as jenis_id',
                'jenis_linen.jenis_nama as jenis_nama',
                'jenis_linen.jenis_berat as jenis_berat',
                'transaksi.transaksi_id_ruangan as ruangan_id',
                'ruangan.ruangan_nama as ruangan_nama',
                DB::raw('COUNT(*) as qty'),
            ])
            ->get();

        return $this->assembleMatrix($rows);
    }

    protected function matrixFromDetailBersih(int $rsId, ?string $start, ?string $end): array
    {
        $legacy = DB::table('bersih')
            ->leftJoin('detail_linen', 'detail_linen.detail_rfid', '=', 'bersih.bersih_rfid')
            ->leftJoin('jenis_linen', 'jenis_linen.jenis_id', '=', 'detail_linen.detail_id_jenis')
            ->leftJoin('ruangan', 'ruangan.ruangan_id', '=', 'bersih.bersih_id_ruangan')
            ->where('bersih.bersih_status', 'BERSIH')
            ->where('bersih.bersih_id_rs', $rsId)
            ->when(! empty($start), fn ($q) => $q->whereDate('bersih.bersih_report', '>=', $start))
            ->when(! empty($end), fn ($q) => $q->whereDate('bersih.bersih_report', '<=', $end))
            ->groupBy('detail_linen.detail_id_jenis', 'jenis_linen.jenis_nama', 'jenis_linen.jenis_berat', 'bersih.bersih_id_ruangan', 'ruangan.ruangan_nama')
            ->select([
                'detail_linen.detail_id_jenis as jenis_id',
                'jenis_linen.jenis_nama as jenis_nama',
                'jenis_linen.jenis_berat as jenis_berat',
                'bersih.bersih_id_ruangan as ruangan_id',
                'ruangan.ruangan_nama as ruangan_nama',
                DB::raw('COUNT(*) as qty'),
            ])
            ->get();

        // Ekor aliran baru (cetak): BERSIH yang report-nya di luar jangkauan legacy.
        $legacyMax = DB::table('bersih')->max('bersih_report');
        $fresh = collect();
        if ($legacyMax && (empty($end) || $end > $legacyMax)) {
            $from = ! empty($start) ? max($start, date('Y-m-d', strtotime($legacyMax.' +1 day'))) : date('Y-m-d', strtotime($legacyMax.' +1 day'));
            $fresh = DB::table('detail_linen')
                ->leftJoin('jenis_linen', 'jenis_linen.jenis_id', '=', 'detail_linen.detail_id_jenis')
                ->leftJoin('ruangan', 'ruangan.ruangan_id', '=', 'detail_linen.detail_id_ruangan')
                ->where('detail_linen.detail_id_rs', $rsId)
                ->where('detail_linen.detail_status_linen', 'BERSIH')
                ->whereDate('detail_linen.detail_report', '>=', $from)
                ->when(! empty($end), fn ($q) => $q->whereDate('detail_linen.detail_report', '<=', $end))
                ->groupBy('detail_linen.detail_id_jenis', 'jenis_linen.jenis_nama', 'jenis_linen.jenis_berat', 'detail_linen.detail_id_ruangan', 'ruangan.ruangan_nama')
                ->select([
                    'detail_linen.detail_id_jenis as jenis_id',
                    'jenis_linen.jenis_nama as jenis_nama',
                    'jenis_linen.jenis_berat as jenis_berat',
                    'detail_linen.detail_id_ruangan as ruangan_id',
                    'ruangan.ruangan_nama as ruangan_nama',
                    DB::raw('COUNT(*) as qty'),
                ])
                ->get();
        }

        return $this->assembleMatrix($legacy->concat($fresh));
    }

    private function assembleMatrix($rows): array
    {
        $locations = [];
        $linens = [];
        $matrix = [];

        foreach ($rows as $row) {
            $locKey = $row->ruangan_id ?? 'null';
            $jenisKey = (string) ($row->jenis_id ?? 'null');

            $locations[$locKey] = $row->ruangan_nama ?? 'Belum Register';

            if (! isset($linens[$jenisKey])) {
                $linens[$jenisKey] = [
                    'nama' => $row->jenis_nama ?? 'Tanpa Jenis',
                    'berat' => (float) ($row->jenis_berat ?? 0),
                ];
            }

            $matrix[$jenisKey][$locKey] = ($matrix[$jenisKey][$locKey] ?? 0) + (int) $row->qty;
        }

        asort($locations);
        uasort($linens, fn ($a, $b) => strcmp($a['nama'], $b['nama']));

        $rowTotal = [];
        $rowKg = [];
        $colTotal = array_fill_keys(array_keys($locations), 0);
        $grandQty = 0;
        $grandKg = 0.0;

        foreach ($linens as $jenisKey => $linen) {
            $qty = array_sum($matrix[$jenisKey] ?? []);
            $kg = $qty * $linen['berat'];
            $rowTotal[$jenisKey] = $qty;
            $rowKg[$jenisKey] = $kg;
            $grandQty += $qty;
            $grandKg += $kg;

            foreach ($matrix[$jenisKey] ?? [] as $locKey => $cell) {
                $colTotal[$locKey] += $cell;
            }
        }

        return compact('locations', 'linens', 'matrix', 'rowTotal', 'rowKg', 'colTotal', 'grandQty', 'grandKg');
    }
}
