<?php

namespace App\Http\Controllers;

use App\Concerns\ControllerTrait;
use App\Enums\TransactionType;
use App\Http\Controllers\Concerns\BuildsRekapMatrix;
use App\Http\Requests\GeneralRequest;
use App\Models\Rs;
use Illuminate\Http\Request;

/**
 * Report Rekap Kotor — adopsi andalan ReportRekapKotorController.
 *
 * Matriks: baris = jenis linen, kolom = ruangan, sel = jumlah kotor (pcs),
 * plus total pcs + total kg per baris dan footer total per kolom.
 *
 * Sumber: tabel `transaksi` (status KOTOR, 1 baris = 1 scan RFID) digrup
 * per jenis × ruangan di SQL — hasilnya kecil (agregat), jadi aman untuk
 * data besar tanpa chunk/streaming. Kg = qty × jenis_berat.
 *
 * Tanpa $this->model (seperti BersihController) sehingga lolos
 * GeneralRequest::authorize() dan tidak butuh Policy baru.
 */
class ReportRekapKotorController extends Controller
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

        $filename = 'rekap-kotor-'.now()->format('Ymd-His').'.xls';

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
        return $this->matrixFromTransaksi(
            (int) $filter['rs_id'],
            TransactionType::KOTOR,
            $filter['start_rekap'] ?? null,
            $filter['end_rekap'] ?? null
        );
    }
}
