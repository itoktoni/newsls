<?php

namespace App\Http\Controllers;

use App\Concerns\ControllerTrait;
use App\Http\Controllers\Concerns\BuildsRekapMatrix;
use App\Http\Requests\GeneralRequest;
use App\Models\Rs;
use Illuminate\Http\Request;

/**
 * Report Rekap Bersih — adopsi andalan ReportRekapBersihController.
 *
 * Matriks: baris = jenis linen, kolom = ruangan, sel = jumlah bersih (pcs),
 * plus total pcs + kg. Sumber `detail_linen` status BERSIH (delivery tidak
 * mencatat transaksi), tanggal = detail_report.
 *
 * Tanpa $this->model (seperti BersihController) sehingga lolos
 * GeneralRequest::authorize() dan tidak butuh Policy baru.
 */
class ReportRekapBersihController extends Controller
{
    use BuildsRekapMatrix;
    use ControllerTrait;

    public function getTable(GeneralRequest $request)
    {
        // Hanya form filter — hasil dibuka di getPrint (tab baru).
        return $this->views($this->template(), $this->share());
    }

    public function getPrint(Request $request)
    {
        set_time_limit(0);

        $validated = $request->validate([
            'rs_id' => 'required|integer|exists:rs,rs_id',
            'start_rekap' => 'nullable|date',
            'end_rekap' => 'nullable|date|after_or_equal:start_rekap',
        ]);

        $rs = Rs::where('rs_id', $validated['rs_id'])->firstOrFail();

        return $this->views($this->template(), array_merge($this->share(), [
            'rs' => $rs,
            'start' => $validated['start_rekap'] ?? null,
            'end' => $validated['end_rekap'] ?? null,
        ], $this->buildMatrix($validated)));
    }

    /**
     * Export Excel via HTML table (dibuka langsung di Excel) —
     * matriks agregat kecil, tanpa paket tambahan.
     */
    public function getExportExcel(Request $request)
    {
        set_time_limit(0);

        $validated = $request->validate([
            'rs_id' => 'required|integer|exists:rs,rs_id',
            'start_rekap' => 'nullable|date',
            'end_rekap' => 'nullable|date|after_or_equal:start_rekap',
        ]);

        $rs = Rs::where('rs_id', $validated['rs_id'])->firstOrFail();

        $filename = 'rekap-bersih-'.now()->format('Ymd-His').'.xls';

        return response()
            ->view($this->template('excel'), array_merge($this->share(), [
                'rs' => $rs,
                'start' => $validated['start_rekap'] ?? null,
                'end' => $validated['end_rekap'] ?? null,
            ], $this->buildMatrix($validated)), 200, [
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

    private function buildMatrix(array $filter): array
    {
        return $this->matrixFromDetailBersih(
            (int) $filter['rs_id'],
            $filter['start_rekap'] ?? null,
            $filter['end_rekap'] ?? null
        );
    }
}
