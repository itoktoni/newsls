<?php

namespace App\Http\Controllers;

use App\Enums\TransactionType;
use App\Http\Controllers\Concerns\BuildsOpnameReports;
use Illuminate\Http\Request;

/**
 * Hilang Opname — ketemu=0 dan status BERSIH (seharusnya di RS, belum terbaca saat SO).
 * Adopsi andalan ReportOpnameHilangController.
 */
class ReportOpnameHilangController extends Controller
{
    use BuildsOpnameReports;

    public function getTable(Request $request)
    {
        return view('pages.report-opname-hilang.table', $this->reportShare());
    }

    public function getPrint(Request $request)
    {
        set_time_limit(0);
        $opname = $this->reportOpname($request);
        $details = $this->reportDetails($opname)
            ->filter(fn ($r) => (int) $r->opname_detail_ketemu === 0
                && $r->opname_detail_transaksi === TransactionType::BERSIH)
            ->values();

        return view('pages.report-opname-hilang.print', $this->reportShare([
            'opname' => $opname,
            'details' => $details,
            'data' => $details,
        ]));
    }

    public function getExportExcel(Request $request)
    {
        set_time_limit(0);
        $opname = $this->reportOpname($request);
        $details = $this->reportDetails($opname)
            ->filter(fn ($r) => (int) $r->opname_detail_ketemu === 0
                && $r->opname_detail_transaksi === TransactionType::BERSIH)
            ->values();

        return $this->reportExport('pages.report-opname-hilang.print', [
            'opname' => $opname,
            'details' => $details,
            'data' => $details,
        ], 'opname-hilang-'.now()->format('Ymd-His').'.xls');
    }
}
