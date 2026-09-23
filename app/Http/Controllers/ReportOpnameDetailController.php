<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\BuildsOpnameReports;
use Illuminate\Http\Request;

class ReportOpnameDetailController extends Controller
{
    use BuildsOpnameReports;

    public function getTable(Request $request)
    {
        return view('pages.report-opname-detail.table', $this->reportShare());
    }

    public function getPrint(Request $request)
    {
        set_time_limit(0);
        $opname = $this->reportOpname($request);
        $details = $this->reportDetails($opname);

        return view('pages.report-opname-detail.print', $this->reportShare([
            'opname' => $opname,
            'details' => $details,
        ]));
    }

    public function getExportExcel(Request $request)
    {
        set_time_limit(0);
        $opname = $this->reportOpname($request);
        $details = $this->reportDetails($opname);

        return $this->reportExport('pages.report-opname-detail.print', [
            'opname' => $opname,
            'details' => $details,
        ], 'opname-detail-'.now()->format('Ymd-His').'.xls');
    }
}
