<?php

namespace App\Http\Controllers;

use App\Concerns\ControllerTrait;
use App\Http\Controllers\Concerns\BuildsDateSeries;
use App\Http\Requests\GeneralRequest;
use App\Models\Rs;
use Carbon\Carbon;
use Illuminate\Http\Request;

/**
 * Report Invoice — adopsi andalan ReportInvoiceController.
 *
 * Baris = jenis linen, kolom = tanggal (qty bersih), plus Type
 * (CUCI/RENTAL dominan), Harga (rs_harga_cuci → sewa), Berat, QTY,
 * Total Kg, Total Rupiah (harga × kg).
 *
 * Bersih = detail BERSIH per tanggal report. Tanpa $this->model
 * sehingga lolos authorize.
 */
class ReportInvoiceController extends Controller
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
            'harga' => $rs->rs_harga_cuci ?? $rs->rs_harga_sewa ?? 0,
        ], $this->buildInvoice($validated, $rs)));
    }

    public function getExportExcel(Request $request)
    {
        set_time_limit(0);

        $validated = $this->validatePeriode($request);
        $rs = Rs::where('rs_id', $validated['rs_id'])->firstOrFail();

        $filename = 'invoice-'.now()->format('Ymd-His').'.xls';

        return response()
            ->view($this->template('excel'), array_merge($this->share(), [
                'rs' => $rs,
                'start' => $validated['start_date'],
                'end' => $validated['end_date'],
                'harga' => $rs->rs_harga_cuci ?? $rs->rs_harga_sewa ?? 0,
            ], $this->buildInvoice($validated, $rs)), 200, [
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
     * @return array{dates: array, linens: array, qty: array, berat: array, cuci: array, rowQty: array, rowKg: array, rowRp: array, sumQty: int, sumKg: float, sumRp: float}
     */
    private function buildInvoice(array $filter, Rs $rs): array
    {
        $rsId = (int) $filter['rs_id'];
        $harga = (float) ($rs->rs_harga_cuci ?? $rs->rs_harga_sewa ?? 0);
        $dates = $this->dateList($filter['start_date'], $filter['end_date']);
        $bersih = $this->bersihSeries($rsId, $filter['start_date'], $filter['end_date']);
        $cuci = $this->cuciMode($rsId, $filter['start_date'], $filter['end_date']);

        $linens = $bersih['names'];
        $qty = $bersih['qty'];
        $berat = $bersih['berat'];

        $rowQty = [];
        $rowKg = [];
        $rowRp = [];
        $sumQty = 0;
        $sumKg = 0.0;
        $sumRp = 0.0;

        foreach ($linens as $key => $nama) {
            $q = 0;
            foreach ($dates as $tgl) {
                $q += $qty[$tgl][$key] ?? 0;
            }
            $kg = $q * ($berat[$key] ?? 0);
            $rp = $kg * $harga;
            $rowQty[$key] = $q;
            $rowKg[$key] = $kg;
            $rowRp[$key] = $rp;
            $sumQty += $q;
            $sumKg += $kg;
            $sumRp += $rp;
        }

        return compact('dates', 'linens', 'qty', 'berat', 'cuci', 'rowQty', 'rowKg', 'rowRp', 'sumQty', 'sumKg', 'sumRp');
    }
}
