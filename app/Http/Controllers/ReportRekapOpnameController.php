<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\BuildsOpnameReports;
use Illuminate\Http\Request;

/**
 * Report Rekap Opname — matriks SA (snapshot) / SO (ketemu) per jenis × ruangan.
 * Adopsi andalan ReportRekapOpnameController + ViewOpname.
 */
class ReportRekapOpnameController extends Controller
{
    use BuildsOpnameReports;

    public function getTable(Request $request)
    {
        return view('pages.report-rekap-opname.table', $this->reportShare());
    }

    public function getPrint(Request $request)
    {
        set_time_limit(0);
        $opname = $this->reportOpname($request);
        $details = $this->reportDetails($opname);
        $matrix = $this->reportRekapMatrix($details);
        $jenisId = $request->input('jenis_id');

        if (! empty($jenisId)) {
            $details = $details->filter(fn ($r) => (int) ($r->hasView?->detail_id_jenis ?? 0) === (int) $jenisId)->values();
            $matrix = $this->reportRekapMatrix($details);
        }

        return view('pages.report-rekap-opname.print', $this->reportShare([
            'opname' => $opname,
            'details' => $details,
            'locations' => $matrix['locations'],
            'linens' => $matrix['linens'],
            'sa' => $matrix['sa'],
            'so' => $matrix['so'],
        ]));
    }

    public function getExportExcel(Request $request)
    {
        set_time_limit(0);
        $opname = $this->reportOpname($request);
        $details = $this->reportDetails($opname);
        $matrix = $this->reportRekapMatrix($details);
        $jenisId = $request->input('jenis_id');
        if (! empty($jenisId)) {
            $details = $details->filter(fn ($r) => (int) ($r->hasView?->detail_id_jenis ?? 0) === (int) $jenisId)->values();
            $matrix = $this->reportRekapMatrix($details);
        }

        return $this->reportExport('pages.report-rekap-opname.print', [
            'opname' => $opname,
            'details' => $details,
            'locations' => $matrix['locations'],
            'linens' => $matrix['linens'],
            'sa' => $matrix['sa'],
            'so' => $matrix['so'],
        ], 'rekap-opname-'.now()->format('Ymd-His').'.xls');
    }
}
