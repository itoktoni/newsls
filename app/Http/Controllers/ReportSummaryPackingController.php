<?php

namespace App\Http\Controllers;

use App\Concerns\ControllerTrait;
use App\Http\Requests\GeneralRequest;
use App\Models\Rs;
use App\Models\WebsiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Report Summary Packing — rekap outstanding PACKING per RS + jenis linen.
 *
 * Satu baris per (RS, jenis): total pcs yang sedang di packing.
 * Hitungan live dari tabel `outstanding` (tanpa tabel perantara).
 *
 * Tanpa $this->model sehingga lolos authorize.
 */
class ReportSummaryPackingController extends Controller
{
    use ControllerTrait;

    public function getTable(GeneralRequest $request)
    {
        return $this->views($this->template(), $this->share());
    }

    public function getPrint(Request $request)
    {
        set_time_limit(0);

        $validated = $this->validateSummary($request);
        $data = $this->summaryQuery($validated)->get();
        $rs = ! empty($validated['rs_id'])
            ? Rs::where('rs_id', $validated['rs_id'])->first()
            : null;

        return $this->views($this->template(), array_merge($this->share(), [
            'data' => $data,
            'rs' => $rs,
            'start' => $validated['start'] ?? null,
            'end' => $validated['end'] ?? null,
        ]));
    }

    public function getExportExcel(Request $request)
    {
        set_time_limit(0);

        $validated = $this->validateSummary($request);
        $data = $this->summaryQuery($validated)->get();

        $rs = ! empty($validated['rs_id'])
            ? Rs::where('rs_id', $validated['rs_id'])->first()
            : null;
        $rsNama = $rs->rs_nama ?? 'Semua Rumah Sakit';
        $logoUrl = WebsiteSetting::fileUrl(config('website.logo'));
        $logoAbs = $logoUrl ? url($logoUrl) : null;
        $periode = (formatDate($validated['start'] ?? null) ?? '-')
            .' - '.(formatDate($validated['end'] ?? null) ?? '-');

        $filename = 'summary-packing-'.now()->format('Ymd-His').'.xls';

        return response()->streamDownload(function () use ($data, $rsNama, $logoAbs, $periode) {
            echo '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel">';
            echo '<head><meta charset="UTF-8"></head><body>';
            echo '<table><tr><td colspan="3"><b>SUMMARY PACKING PER JENIS</b><br><b>RUMAH SAKIT : '.e($rsNama).'</b><br><b>Periode Packing : '.e($periode).'</b></td>';
            echo '<td colspan="2" style="text-align:right;">';
            if ($logoAbs) {
                echo '<img src="'.e($logoAbs).'" alt="Logo" height="60" width="90">';
            }
            echo '</td></tr></table><br>';
            echo '<table border="1"><thead><tr>'
                .'<th>No.</th><th>RUMAH SAKIT</th><th>JENIS LINEN</th><th>TOTAL PACKING</th>'
                .'</tr></thead><tbody>';

            $no = 0;
            $grand = 0;
            foreach ($data as $table) {
                $no++;
                $grand += (int) $table->total;
                echo '<tr><td>'.$no.'</td>'
                    .'<td>'.e($table->rs_nama ?? '-').'</td>'
                    .'<td>'.e($table->jenis_nama ?? '-').'</td>'
                    .'<td>'.e($table->total).'</td></tr>';
            }

            echo '</tbody><tfoot><tr><td colspan="3"><b>TOTAL</b></td><td><b>'.$grand.'</b></td></tr></tfoot></table></body></html>';
        }, $filename, [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }

    protected function share($data = [])
    {
        return array_merge(['rsOptions' => \App\Models\User::rsOptions()], $data);
    }

    private function validateSummary(Request $request): array
    {
        return $request->validate([
            'rs_id' => 'nullable|integer|exists:rs,rs_id',
            'start' => 'nullable|date',
            'end' => 'nullable|date|after_or_equal:start',
        ]);
    }

    private function summaryQuery(array $filter)
    {
        // RS diambil dari outstanding_rs_scan: packing mengupdate rs_scan,
        // bukan rs_ori (PackingDeliveryController::updateOutstandingToPacking).
        return DB::table('outstanding')
            ->leftJoin('detail_linen', 'detail_linen.detail_rfid', '=', 'outstanding.outstanding_rfid')
            ->leftJoin('jenis_linen', 'jenis_linen.jenis_id', '=', 'detail_linen.detail_id_jenis')
            ->leftJoin('rs', 'rs.rs_id', '=', 'outstanding.outstanding_rs_scan')
            ->where('outstanding.outstanding_status_proses', 'PACKING')
            ->when(! empty($filter['rs_id']), fn ($q) => $q->where('outstanding.outstanding_rs_scan', $filter['rs_id']))
            ->when(! empty($filter['start']), fn ($q) => $q->whereDate('outstanding.outstanding_updated_at', '>=', $filter['start']))
            ->when(! empty($filter['end']), fn ($q) => $q->whereDate('outstanding.outstanding_updated_at', '<=', $filter['end']))
            ->groupBy('outstanding.outstanding_rs_scan', 'rs.rs_nama', 'detail_linen.detail_id_jenis', 'jenis_linen.jenis_nama')
            ->orderBy('rs.rs_nama')
            ->orderBy('jenis_linen.jenis_nama')
            ->select([
                'rs.rs_nama as rs_nama',
                'jenis_linen.jenis_nama as jenis_nama',
                DB::raw('COUNT(*) as total'),
            ]);
    }
}
