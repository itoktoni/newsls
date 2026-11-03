<?php

namespace App\Http\Controllers;

use App\Concerns\ControllerTrait;
use App\Http\Controllers\Concerns\BuildsDateSeries;
use App\Http\Requests\GeneralRequest;
use App\Models\Rs;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Report Invoice — adopsi andalan ReportInvoiceController.
 *
 * Baris = jenis linen, kolom = tanggal (qty kotor), plus Type
 * (CUCI/RENTAL dominan), Harga (rs_harga_cuci → sewa), Berat, QTY,
 * Total Estimasi Kg (qty × jenis_berat), Total Rupiah (harga × kg),
 * ditambah baris TIMBANGAN harian (SUM transaksi_master.master_qty).
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
            'rsOptions' => User::rsOptions(),
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
     * Matriks harian berbasis KOTOR (yang ditagih), bukan bersih delivery.
     *
     * - Estimasi per jenis/tanggal = COUNT(transaksi KOTOR) × jenis_berat.
     * - Timbangan per tanggal = SUM(transaksi_master.master_qty) status KOTOR.
     * Retur/rewash tidak ikut (bukan cucian yang ditagih).
     * Rupiah dihitung dari keduanya (harga × kg).
     *
     * @return array{dates: array, linens: array, qty: array, berat: array, cuci: array, rowQty: array, rowKg: array, rowRp: array, sumQty: int, sumKg: float, sumRp: float, timbanganHarian: array, timbangan: ?float, nTimbang: int, estimasiKg: float, selisihKg: ?float, rpTimbangan: ?float, rpEstimasi: float}
     */
    private function buildInvoice(array $filter, Rs $rs): array
    {
        $rsId = (int) $filter['rs_id'];
        $harga = (float) ($rs->rs_harga_cuci ?? $rs->rs_harga_sewa ?? 0);
        $dates = $this->dateList($filter['start_date'], $filter['end_date']);
        $kotor = $this->kotorSeries($rsId, $filter['start_date'], $filter['end_date']);
        $cuci = $this->cuciMode($rsId, $filter['start_date'], $filter['end_date']);

        $linens = $kotor['names'];
        $qty = $kotor['qty'];
        $berat = $kotor['berat'];

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

        // Timbangan kotor per tanggal + total, dari transaksi_master.master_qty.
        $timbanganHarian = [];
        $timbangan = null;
        $nTimbang = 0;
        if (Schema::hasTable('transaksi_master')) {
            $perDate = DB::table('transaksi_master')
                ->where('master_rs_id', $rsId)
                ->where('master_status', 'KOTOR')
                ->whereDate('master_tanggal', '>=', $filter['start_date'])
                ->whereDate('master_tanggal', '<=', $filter['end_date'])
                ->groupBy('master_tanggal')
                ->selectRaw('master_tanggal as tgl, SUM(master_qty) as timbangan, SUM(CASE WHEN master_qty IS NULL THEN 0 ELSE 1 END) as n_timbang')
                ->get();
            foreach ($perDate as $row) {
                $tgl = is_string($row->tgl) ? substr($row->tgl, 0, 10) : $row->tgl->format('Y-m-d');
                $timbanganHarian[$tgl] = $row->timbangan !== null ? (float) $row->timbangan : null;
                $nTimbang += (int) ($row->n_timbang ?? 0);
                if ($row->timbangan !== null) {
                    $timbangan = ($timbangan ?? 0.0) + (float) $row->timbangan;
                }
            }
        }

        $estimasiKg = $sumKg;
        $rpEstimasi = $sumRp;
        $rpTimbangan = $timbangan !== null ? $timbangan * $harga : null;
        $selisihKg = $timbangan !== null ? round($timbangan - $estimasiKg, 3) : null;

        return compact('dates', 'linens', 'qty', 'berat', 'cuci', 'rowQty', 'rowKg', 'rowRp', 'sumQty', 'sumKg', 'sumRp', 'timbanganHarian', 'timbangan', 'nTimbang', 'estimasiKg', 'selisihKg', 'rpTimbangan', 'rpEstimasi');
    }
}
