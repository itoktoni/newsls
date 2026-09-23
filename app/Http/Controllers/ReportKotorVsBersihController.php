<?php

namespace App\Http\Controllers;

use App\Concerns\ControllerTrait;
use App\Http\Controllers\Concerns\BuildsDateSeries;
use App\Http\Requests\GeneralRequest;
use App\Models\Rs;
use Carbon\Carbon;
use Illuminate\Http\Request;

/**
 * Report Kotor vs Bersih — per tanggal ke samping, tiap tanggal dua
 * kolom berdampingan (Kotor | Bersih), baris = jenis linen.
 *
 * Kotor = transaksi KOTOR (scan RS) per tanggal transaksi.
 * Bersih = detail BERSIH per tanggal report.
 *
 * Tanpa $this->model sehingga lolos authorize.
 */
class ReportKotorVsBersihController extends Controller
{
    use BuildsDateSeries;
    use ControllerTrait;

    public function getTable(GeneralRequest $request)
    {
        // Hanya form filter — hasil dibuka di getPrint (tab baru).
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

        $filename = 'kotor-vs-bersih-'.now()->format('Ymd-His').'.xls';

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
        $default = [
            'rsOptions' => \App\Models\User::rsOptions(),
        ];

        return array_merge($default, $data);
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

    /**
     * Aturan bisnis: kotor hari ini baru dikirim bersih besok — kolom
     * Bersih tanggal T = linen bersih tanggal report T+1 (H+1).
     * S = selisih (Kotor − Bersih): kurang berapa yang dikirim laundry.
     *
     * @return array{dates: array, linens: array, kotor: array, bersih: array, rowKotor: array, rowBersih: array, rowSelisih: array, colKotor: array, colBersih: array, colSelisih: array, grandKotor: int, grandBersih: int, grandSelisih: int}
     */
    private function buildComparison(array $filter): array
    {
        $rsId = (int) $filter['rs_id'];
        $dates = $this->dateList($filter['start_date'], $filter['end_date']);
        $kotor = $this->kotorSeries($rsId, $filter['start_date'], $filter['end_date']);
        // Bersih digeser H+1: query report start+1 s/d end+1.
        $bersih = $this->bersihSeries(
            $rsId,
            Carbon::parse($filter['start_date'])->addDay()->format('Y-m-d'),
            Carbon::parse($filter['end_date'])->addDay()->format('Y-m-d')
        );

        $linens = [];
        foreach ([$kotor['names'], $bersih['names']] as $names) {
            foreach ($names as $key => $nama) {
                $linens[$key] = $nama;
            }
        }
        asort($linens);

        $rowKotor = array_fill_keys(array_keys($linens), 0);
        $rowBersih = array_fill_keys(array_keys($linens), 0);
        $colKotor = array_fill_keys($dates, 0);
        $colBersih = array_fill_keys($dates, 0);
        $grandKotor = 0;
        $grandBersih = 0;

        // Peta bersih digeser ke tanggal kolom (T = report T+1) agar
        // sel tabel ($bersih['qty'][$tgl]) selaras dengan total.
        $shifted = [];
        foreach ($dates as $tgl) {
            $besok = Carbon::parse($tgl)->addDay()->format('Y-m-d');
            foreach ($linens as $key => $nama) {
                $shifted[$tgl][$key] = $bersih['qty'][$besok][$key] ?? 0;
            }
        }
        $bersih['qty'] = $shifted;

        $rowSelisih = array_fill_keys(array_keys($linens), 0);
        $colSelisih = array_fill_keys($dates, 0);
        $grandSelisih = 0;

        foreach ($linens as $key => $nama) {
            foreach ($dates as $tgl) {
                $k = $kotor['qty'][$tgl][$key] ?? 0;
                $b = $shifted[$tgl][$key] ?? 0;
                $s = $k - $b;
                $rowKotor[$key] += $k;
                $rowBersih[$key] += $b;
                $rowSelisih[$key] += $s;
                $colKotor[$tgl] += $k;
                $colBersih[$tgl] += $b;
                $colSelisih[$tgl] += $s;
                $grandKotor += $k;
                $grandBersih += $b;
                $grandSelisih += $s;
            }
        }

        return compact('dates', 'linens', 'kotor', 'bersih', 'rowKotor', 'rowBersih', 'rowSelisih', 'colKotor', 'colBersih', 'colSelisih', 'grandKotor', 'grandBersih', 'grandSelisih');
    }
}
