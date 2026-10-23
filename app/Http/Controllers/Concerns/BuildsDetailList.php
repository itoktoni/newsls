<?php

namespace App\Http\Controllers\Concerns;

use App\Models\Rs;
use App\Models\WebsiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * List detail transaksi per RFID (adopsi andalan ReportDetail*Controller).
 *
 * Controller pemakai cukup definisikan 3 konstanta:
 * - STATUS       : nilai transaksi_status (KOTOR/REJECT/REWASH)
 * - TITLE        : judul kop, mis. 'DETAIL TRANSAKSI KOTOR'
 * - FILE_PREFIX  : awalan nama file excel, mis. 'detail-kotor'
 *
 * Proteksi data besar (ala andalan): getPrint menghitung dulu —
 * melebihi REPORT_CHUNK (default 10000) langsung streaming Excel
 * per chunk 1000 + flush, tanpa limit.
 */
trait BuildsDetailList
{
    use BuildsBeratSummary;

    public function getPrint(Request $request)
    {
        set_time_limit(0);

        $validated = $this->validateDetail($request);
        $query = $this->detailBaseQuery($validated);

        $threshold = (int) env('REPORT_CHUNK', 10000);
        if ($threshold > 0 && (clone $query)->count() > $threshold) {
            return $this->getExportExcel($request);
        }

        $data = (clone $query)->orderBy('transaksi.transaksi_id')->get();

        $rs = Rs::where('rs_id', $validated['rs_id'])->firstOrFail();

        return $this->views($this->template(), array_merge($this->share(), [
            'title' => static::TITLE,
            'data' => $data,
            'rs' => $rs,
            'start' => $validated['start_date'] ?? null,
            'end' => $validated['end_date'] ?? null,
            'berat' => $this->beratSummary(
                (int) $validated['rs_id'],
                static::STATUS,
                $validated['start_date'] ?? null,
                $validated['end_date'] ?? null
            ),
        ]));
    }

    public function getExportExcel(Request $request)
    {
        set_time_limit(0);

        $validated = $this->validateDetail($request);

        $rs = Rs::where('rs_id', $validated['rs_id'])->firstOrFail();
        $rsNama = $rs->rs_nama ?? 'Semua Rumah Sakit';
        $logoUrl = WebsiteSetting::fileUrl(config('website.logo'));
        $logoAbs = $logoUrl ? url($logoUrl) : null;
        $periode = (formatDate($validated['start_date'] ?? null) ?? '-')
            .' - '.(formatDate($validated['end_date'] ?? null) ?? '-');
        $title = static::TITLE;

        $filename = static::FILE_PREFIX.'-'.now()->format('Ymd-His').'.xls';

        $berat = $this->beratSummary(
            (int) $validated['rs_id'],
            static::STATUS,
            $validated['start_date'] ?? null,
            $validated['end_date'] ?? null
        );

        return response()->streamDownload(function () use ($validated, $rsNama, $logoAbs, $periode, $title, $berat) {
            echo '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel">';
            echo '<head><meta charset="UTF-8"></head><body>';
            echo '<table><tr><td colspan="11"><b>'.e($title).'</b><br><b>RUMAH SAKIT : '.e($rsNama).'</b><br><b>Periode : '.e($periode).'</b></td>';
            echo '<td colspan="2" style="text-align:right;">';
            if ($logoAbs) {
                echo '<img src="'.e($logoAbs).'" alt="Logo" height="60" width="90">';
            }
            echo '</td></tr></table><br>';
            echo '<table border="1"><tr><td colspan="13"><b>RINGKASAN BERAT TIMBANGAN</b> ('.e(($berat['n_transaksi'] ?? 0).' transaksi, ditimbang: '.($berat['n_timbang'] ?? 0)).')</td></tr>'
                .'<tr><td colspan="3"><b>Kotor Timbangan (Kg)</b></td><td colspan="3">'.e(($berat['kotor'] ?? null) !== null ? $berat['kotor'] : 'belum ditimbang').'</td>'
                .'<td colspan="3"><b>Bersih Hitung (Kg)</b></td><td colspan="4">'.e($berat['bersih'] ?? 0).'</td></tr>'
                .'<tr><td colspan="3"><b>Selisih (Kg)</b></td><td colspan="3">'.e(($berat['selisih'] ?? null) !== null ? $berat['selisih'] : '-').'</td>'
                .'<td colspan="3"><b>N Transaksi</b></td><td colspan="4">'.e($berat['n_transaksi'] ?? 0).'</td></tr></table><br>';
            echo '<table border="1"><thead><tr>'
                .'<th>No.</th><th>NO. TRANSAKSI</th><th>NO. RFID</th><th>LINEN</th><th>BERAT (KG)</th>'
                .'<th>RUMAH SAKIT</th><th>RUANGAN</th><th>LOKASI SCAN</th><th>CUCI/RENTAL</th>'
                .'<th>JUMLAH PEMAKAIAN</th><th>TGL PENERIMAAN</th><th>TGL REGISTER</th><th>OPERATOR</th>'
                .'</tr></thead><tbody>';

            $no = 0;
            $totalKg = 0.0;
            $this->detailBaseQuery($validated)
                ->orderBy('transaksi.transaksi_id')
                ->chunk(1000, function ($rows) use (&$no, &$totalKg) {
                    foreach ($rows as $table) {
                        $no++;
                        $totalKg += (float) ($table->jenis_berat ?? 0);
                        echo '<tr><td>'.$no.'</td>'
                            .'<td>'.e($table->transaksi_key).'</td>'
                            .'<td>'.e($table->transaksi_rfid).'</td>'
                            .'<td>'.e($table->jenis_nama ?? '-').'</td>'
                            .'<td>'.e($table->jenis_berat ?? '-').'</td>'
                            .'<td>'.e($table->rs_ori_nama ?? '-').'</td>'
                            .'<td>'.e($table->ruangan_nama ?? '-').'</td>'
                            .'<td>'.e($table->rs_scan_nama ?? '-').'</td>'
                            .'<td>'.e($table->detail_status_cuci ?? '-').'</td>'
                            .'<td>'.e($table->detail_total_bersih ?? 0).'</td>'
                            .'<td>'.e(formatDate($table->transaksi_created_at) ?? '-').'</td>'
                            .'<td>'.e(formatDate($table->detail_created_at) ?? '-').'</td>'
                            .'<td>'.e($table->operator_nama ?? '-').'</td></tr>';
                    }
                    flush();
                });

            echo '</tbody><tfoot><tr><th colspan="4">Total</th><th>'.$totalKg.'</th><th colspan="8">'.$no.' baris</th></tr></tfoot></table></body></html>';
        }, $filename, [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }

    private function validateDetail(Request $request): array
    {
        return $request->validate([
            'rs_id' => 'required|integer|exists:rs,rs_id',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);
    }

    private function detailBaseQuery(array $filter)
    {
        return DB::table('transaksi')
            ->join('detail_linen', 'detail_linen.detail_rfid', '=', 'transaksi.transaksi_rfid')
            ->leftJoin('jenis_linen', 'jenis_linen.jenis_id', '=', 'detail_linen.detail_id_jenis')
            ->leftJoin('ruangan', 'ruangan.ruangan_id', '=', 'transaksi.transaksi_id_ruangan')
            ->leftJoin('rs as rs_ori', 'rs_ori.rs_id', '=', 'transaksi.transaksi_rs_ori')
            ->leftJoin('rs as rs_scan', 'rs_scan.rs_id', '=', 'transaksi.transaksi_rs_scan')
            ->leftJoin('users', 'users.id', '=', 'transaksi.transaksi_created_by')
            ->where('transaksi.transaksi_status', static::STATUS)
            ->where('transaksi.transaksi_rs_ori', $filter['rs_id'])
            ->when(! empty($filter['start_date']), fn ($q) => $q->whereDate('transaksi.transaksi_created_at', '>=', $filter['start_date']))
            ->when(! empty($filter['end_date']), fn ($q) => $q->whereDate('transaksi.transaksi_created_at', '<=', $filter['end_date']))
            ->select([
                'transaksi.transaksi_id',
                'transaksi.transaksi_key',
                'transaksi.transaksi_rfid',
                'transaksi.transaksi_created_at',
                'jenis_linen.jenis_nama as jenis_nama',
                'jenis_linen.jenis_berat as jenis_berat',
                'rs_ori.rs_nama as rs_ori_nama',
                'ruangan.ruangan_nama as ruangan_nama',
                'rs_scan.rs_nama as rs_scan_nama',
                'detail_linen.detail_status_cuci',
                'detail_linen.detail_total_bersih',
                'detail_linen.detail_created_at',
                'users.name as operator_nama',
            ]);
    }
}
