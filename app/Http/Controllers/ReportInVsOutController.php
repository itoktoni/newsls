<?php

namespace App\Http\Controllers;

use App\Concerns\ControllerTrait;
use App\Http\Controllers\Concerns\BuildsDateSeries;
use App\Http\Requests\GeneralRequest;
use App\Models\Rs;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

/**
 * Report In vs Out per jenis linen dan tanggal.
 *
 * In = transaksi pada transaksi_grouping_date.
 * Out = bersih pada bersih_report T+1 (dipetakan ke kolom T).
 */
class ReportInVsOutController extends Controller
{
    use BuildsDateSeries;
    use ControllerTrait;

    public function getTable(GeneralRequest $request)
    {
        return $this->views($this->template(), $this->share());
    }

    public function getPrint(Request $request)
    {
        set_time_limit(0);

        $validated = $this->validatePeriode($request);
        $rs = Rs::where('rs_id', $validated['rs_id'])->firstOrFail();

        return $this->views($this->template(), array_merge($this->share(), [
            'rs' => $rs,
            'start' => $validated['start_date'],
            'end' => $validated['end_date'],
        ], $this->buildComparison($validated)));
    }

    public function getExportExcel(Request $request)
    {
        set_time_limit(0);

        $validated = $this->validatePeriode($request);
        $rs = Rs::where('rs_id', $validated['rs_id'])->firstOrFail();
        $filename = 'in-vs-out-'.now()->format('Ymd-His').'.xls';

        return response()
            ->view($this->template('excel'), array_merge($this->share(), [
                'rs' => $rs,
                'start' => $validated['start_date'],
                'end' => $validated['end_date'],
            ], $this->buildComparison($validated)), 200, [
                'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="'.$filename.'"',
            ]);
    }

    protected function share($data = [])
    {
        return array_merge(['rsOptions' => User::rsOptions()], $data);
    }

    private function validatePeriode(Request $request): array
    {
        return $request->validate([
            'rs_id' => 'required|integer|exists:rs,rs_id',
            'start_date' => 'required|date',
            'end_date' => [
                'required',
                'date',
                'after_or_equal:start_date',
                function ($attribute, $value, $fail) use ($request) {
                    $start = $request->input('start_date');
                    if ($start && Carbon::parse($start)->diffInDays(Carbon::parse($value)) > 93) {
                        $fail('Periode maksimal 93 hari agar tabel tetap terbaca.');
                    }
                },
            ],
        ]);
    }

    private function buildComparison(array $filter): array
    {
        $rsId = (int) $filter['rs_id'];
        $dates = $this->dateList($filter['start_date'], $filter['end_date']);
        $in = $this->groupingSeries($rsId, $filter['start_date'], $filter['end_date']);
        $out = $this->bersihSeries(
            $rsId,
            Carbon::parse($filter['start_date'])->addDay()->format('Y-m-d'),
            Carbon::parse($filter['end_date'])->addDay()->format('Y-m-d')
        );

        $linens = array_merge($in['names'], $out['names']);
        asort($linens);

        $rowIn = array_fill_keys(array_keys($linens), 0);
        $rowOut = array_fill_keys(array_keys($linens), 0);
        $rowSelisih = array_fill_keys(array_keys($linens), 0);
        $colIn = array_fill_keys($dates, 0);
        $colOut = array_fill_keys($dates, 0);
        $colSelisih = array_fill_keys($dates, 0);
        $grandIn = 0;
        $grandOut = 0;
        $grandSelisih = 0;

        $shiftedOut = [];
        foreach ($dates as $tgl) {
            $besok = Carbon::parse($tgl)->addDay()->format('Y-m-d');
            foreach ($linens as $key => $nama) {
                $shiftedOut[$tgl][$key] = $out['qty'][$besok][$key] ?? 0;
            }
        }
        $out['qty'] = $shiftedOut;

        foreach ($linens as $key => $nama) {
            foreach ($dates as $tgl) {
                $inQty = $in['qty'][$tgl][$key] ?? 0;
                $outQty = $shiftedOut[$tgl][$key] ?? 0;
                $selisih = $inQty - $outQty;

                $rowIn[$key] += $inQty;
                $rowOut[$key] += $outQty;
                $rowSelisih[$key] += $selisih;
                $colIn[$tgl] += $inQty;
                $colOut[$tgl] += $outQty;
                $colSelisih[$tgl] += $selisih;
                $grandIn += $inQty;
                $grandOut += $outQty;
                $grandSelisih += $selisih;
            }
        }

        return compact('dates', 'linens', 'in', 'out', 'rowIn', 'rowOut', 'rowSelisih', 'colIn', 'colOut', 'colSelisih', 'grandIn', 'grandOut', 'grandSelisih');
    }
}
