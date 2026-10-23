<?php

namespace App\Http\Controllers\Concerns;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Ringkasan berat per RS + status + periode.
 *
 * - kotor   : SUM master_qty timbangan (NULL diabaikan SQL; null bila tak
 *             ada satu pun transaksi yang ditimbang)
 * - bersih  : SUM jenis_berat dihitung LIVE dari baris detail
 *             (transaksi_master → transaksi → detail_linen → jenis_linen),
 *             TIDAK disimpan di master
 * - selisih : kotor - bersih (null bila kotor null)
 *
 * @return array{n_transaksi: int, n_timbang: int, kotor: ?float, bersih: float, selisih: ?float}
 */
trait BuildsBeratSummary
{
    protected function beratSummary(int $rsId, string $status, ?string $start, ?string $end): array
    {
        $empty = ['n_transaksi' => 0, 'n_timbang' => 0, 'kotor' => null, 'bersih' => 0.0, 'selisih' => null];

        if (! Schema::hasTable('transaksi_master')) {
            return $empty;
        }

        $row = $this->masterFilter($rsId, $status, $start, $end)
            ->selectRaw('COUNT(*) as n, SUM(master_qty) as kotor, SUM(CASE WHEN master_qty IS NULL THEN 0 ELSE 1 END) as n_timbang')
            ->first();

        if (! $row || (int) $row->n === 0) {
            return $empty;
        }

        $kotor = $row->kotor !== null ? (float) $row->kotor : null;
        $bersih = (float) $this->masterFilter($rsId, $status, $start, $end)
            ->join('transaksi', 'transaksi.transaksi_key', '=', 'transaksi_master.master_kode')
            ->join('detail_linen', 'detail_linen.detail_rfid', '=', 'transaksi.transaksi_rfid')
            ->join('jenis_linen', 'jenis_linen.jenis_id', '=', 'detail_linen.detail_id_jenis')
            ->sum('jenis_linen.jenis_berat');

        return [
            'n_transaksi' => (int) $row->n,
            'n_timbang' => (int) $row->n_timbang,
            'kotor' => $kotor,
            'bersih' => $bersih,
            'selisih' => $kotor !== null ? round($kotor - $bersih, 3) : null,
        ];
    }

    private function masterFilter(int $rsId, string $status, ?string $start, ?string $end)
    {
        return DB::table('transaksi_master')
            ->where('transaksi_master.master_rs_id', $rsId)
            ->where('transaksi_master.master_status', $status)
            ->when(! empty($start), fn ($q) => $q->whereDate('transaksi_master.master_tanggal', '>=', $start))
            ->when(! empty($end), fn ($q) => $q->whereDate('transaksi_master.master_tanggal', '<=', $end));
    }
}
