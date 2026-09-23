<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\BuildsOpnameReports;
use Illuminate\Http\Request;

/**
 * Report Opname Summary — adopsi andalan:
 * 1) tabel harian SCAN LINEN SAAT SO (group by tanggal)
 * 2) tabel KPI: Register / Scan / belum terbaca RS / Laundry / Total Summary
 */
class ReportOpnameSummaryController extends Controller
{
    use BuildsOpnameReports;

    public function getTable(Request $request)
    {
        $share = $this->reportShare();

        if ($request->filled('opname_id')) {
            $share = array_merge($share, $this->summaryData($request));
        }

        return view('pages.report-opname-summary.table', $share);
    }

    public function getPrint(Request $request)
    {
        set_time_limit(0);

        return view('pages.report-opname-summary.print', $this->summaryData($request));
    }

    public function getExportExcel(Request $request)
    {
        set_time_limit(0);
        $data = $this->summaryData($request);

        return $this->reportExport('pages.report-opname-summary.print', [
            'opname' => $data['opname'],
            'details' => $data['details'],
            'data' => $data['details'],
            'register' => $data['register'],
        ], 'opname-summary-'.now()->format('Ymd-His').'.xls');
    }

    protected function summaryData(Request $request): array
    {
        $opname = $this->reportOpname($request);
        $details = $this->reportDetails($opname);

        // andalan ReportOpnameSummaryController: register = whereNotNull(transaksi)
        $register = $details->whereNotNull('opname_detail_transaksi')->count();

        return $this->reportShare([
            'opname' => $opname,
            'details' => $details,
            'data' => $details,
            'register' => $register,
        ]);
    }
}
