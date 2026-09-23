<?php

namespace App\Http\Controllers;

use App\Concerns\ControllerTrait;
use App\Http\Requests\GeneralRequest;
use App\Models\Rs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Report Penggantian Linen — adopsi andalan ReportPenggantianLinenController.
 *
 * Sumber `ganti_chip` (RFID lama → baru) + detail linen baru + operator.
 * Kolom: linen lama, linen baru, jenis, RS, ruangan, cuci, status register,
 * tanggal penggantian, operator.
 * Proteksi data besar: count dulu, > REPORT_CHUNK otomatis streaming Excel.
 *
 * Tanpa $this->model sehingga lolos authorize.
 */
class ReportPenggantianLinenController extends Controller
{
    use ControllerTrait;

    public function getTable(GeneralRequest $request)
    {
        // Hanya form filter — hasil dibuka di getPrint (tab baru).
        return $this->views($this->template(), $this->share());
    }

    public function getPrint(Request $request)
    {
        set_time_limit(0);

        $validated = $this->validateGanti($request);
        $query = $this->gantiBaseQuery($validated);

        $threshold = (int) env('REPORT_CHUNK', 10000);
        if ($threshold > 0 && (clone $query)->count() > $threshold) {
            return $this->getExportExcel($request);
        }

        $data = (clone $query)->orderBy('ganti_chip.ganti_tanggal')->orderBy('ganti_chip.ganti_id')->get();
        $rs = ! empty($validated['rs_id'])
            ? Rs::where('rs_id', $validated['rs_id'])->first()
            : null;

        return $this->views($this->template(), array_merge($this->share(), [
            'data' => $data,
            'rs' => $rs,
            'start' => $validated['start_date'] ?? null,
            'end' => $validated['end_date'] ?? null,
        ]));
    }

    protected function excelPayload(Request $request): array
    {
        $validated = $this->validateGanti($request);

        $rs = ! empty($validated['rs_id'])
            ? Rs::where('rs_id', $validated['rs_id'])->first()
            : null;

        $data = $this->gantiBaseQuery($validated)
            ->orderBy('ganti_chip.ganti_tanggal')
            ->orderBy('ganti_chip.ganti_id')
            ->get();

        $filename = 'penggantian-linen-'.now()->format('Ymd-His').'.xls';

        return [$filename, [
            'data' => $data,
            'rs' => $rs,
            'start' => $validated['start_date'] ?? null,
            'end' => $validated['end_date'] ?? null,
        ]];
    }

    protected function share($data = [])
    {
        $default = [
            'rsOptions' => \App\Models\User::rsOptions(),
        ];

        return array_merge($default, $data);
    }

    private function validateGanti(Request $request): array
    {
        return $request->validate([
            'rs_id' => 'nullable|integer|exists:rs,rs_id',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);
    }

    private function gantiBaseQuery(array $filter)
    {
        return DB::table('ganti_chip')
            ->leftJoin('detail_linen', 'detail_linen.detail_rfid', '=', 'ganti_chip.ganti_rfid_baru')
            ->leftJoin('jenis_linen', 'jenis_linen.jenis_id', '=', 'detail_linen.detail_id_jenis')
            ->leftJoin('ruangan', 'ruangan.ruangan_id', '=', 'detail_linen.detail_id_ruangan')
            ->leftJoin('rs', 'rs.rs_id', '=', 'detail_linen.detail_id_rs')
            ->leftJoin('users', 'users.id', '=', 'ganti_chip.ganti_by')
            ->when(! empty($filter['rs_id']), fn ($q) => $q->where('detail_linen.detail_id_rs', $filter['rs_id']))
            ->when(! empty($filter['start_date']), fn ($q) => $q->whereDate('ganti_chip.ganti_tanggal', '>=', $filter['start_date']))
            ->when(! empty($filter['end_date']), fn ($q) => $q->whereDate('ganti_chip.ganti_tanggal', '<=', $filter['end_date']))
            ->select([
                'ganti_chip.ganti_rfid_lama',
                'ganti_chip.ganti_rfid_baru',
                'ganti_chip.ganti_tanggal',
                'jenis_linen.jenis_nama as jenis_nama',
                'rs.rs_nama as rs_nama',
                'ruangan.ruangan_nama as ruangan_nama',
                'detail_linen.detail_status_cuci',
                'detail_linen.detail_status_register',
                'users.name as operator_nama',
            ]);
    }
}
