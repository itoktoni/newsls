<?php

namespace App\Http\Controllers;

use App\Concerns\ControllerTrait;
use App\Enums\LinenStatusEnum;
use App\Http\Requests\GeneralRequest;
use App\Models\Rs;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Report Cycle Linen — linen BERSIH yang sudah lama tidak dipakai.
 *
 * Sumber `detail_linen` berstatus BERSIH (= fisik di rumah sakit) dengan
 * `detail_report` (tanggal terakhir dipakai/delivery) lebih tua dari cutoff:
 * preset 7/14/30/90 hari (default 7) atau tanggal eksplisit.
 * Linen yang belum pernah punya detail_report ikut tampil paling atas.
 * Kolom: RFID, linen, RS, ruangan, report terakhir, lama diam (hari),
 * jumlah pemakaian.
 *
 * Tanpa $this->model sehingga lolos authorize.
 */
class ReportCycleLinenController extends Controller
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

        $validated = $this->validateCycle($request);
        $query = $this->cycleBaseQuery($validated);

        $threshold = (int) env('REPORT_CHUNK', 10000);
        if ($threshold > 0 && (clone $query)->count() > $threshold) {
            return $this->getExportExcel($request);
        }

        $data = (clone $query)->orderBy('detail_linen.detail_report')->get();
        $rs = ! empty($validated['rs_id'])
            ? Rs::where('rs_id', $validated['rs_id'])->first()
            : null;

        return $this->views($this->template(), array_merge($this->share(), [
            'data' => $data,
            'rs' => $rs,
            'start' => null,
            'end' => null,
            'cycleLabel' => $this->cycleLabel($validated),
        ]));
    }

    protected function excelPayload(Request $request): array
    {
        $validated = $this->validateCycle($request);

        $rs = ! empty($validated['rs_id'])
            ? Rs::where('rs_id', $validated['rs_id'])->first()
            : null;

        $data = $this->cycleBaseQuery($validated)
            ->orderBy('detail_linen.detail_report')
            ->get();

        $filename = 'cycle-linen-'.now()->format('Ymd-His').'.xls';

        return [$filename, [
            'data' => $data,
            'rs' => $rs,
            'start' => null,
            'end' => null,
            'cycleLabel' => $this->cycleLabel($validated),
        ]];
    }

    protected function share($data = [])
    {
        $default = [
            'rsOptions' => User::rsOptions(),
            'hariOptions' => ['7' => '7 Hari', '14' => '14 Hari', '30' => '30 Hari', '90' => '90 Hari'],
        ];

        return array_merge($default, $data);
    }

    private function validateCycle(Request $request): array
    {
        $validated = $request->validate([
            'rs_id' => 'nullable|integer|exists:rs,rs_id',
            'hari' => 'nullable|integer|in:7,14,30,90',
            'cycle_sejak' => 'nullable|date|before_or_equal:today',
        ]);

        // Default 7 hari supaya bukaan pertama langsung menampilkan yang mengendap seminggu.
        $validated['hari'] ??= empty($validated['cycle_sejak']) ? 7 : null;

        return $validated;
    }

    /**
     * Cutoff "tidak dipakai sejak": tanggal eksplisit menang atas preset hari.
     */
    private function cycleCutoff(array $filter): string
    {
        if (! empty($filter['cycle_sejak'])) {
            return $filter['cycle_sejak'];
        }

        return now()->subDays((int) ($filter['hari'] ?? 7))->format('Y-m-d');
    }

    private function cycleLabel(array $filter): string
    {
        if (! empty($filter['cycle_sejak'])) {
            return 'Tidak dipakai sejak '.formatDate($filter['cycle_sejak']);
        }

        return 'Tidak dipakai > '.(int) ($filter['hari'] ?? 7).' hari';
    }

    private function cycleBaseQuery(array $filter)
    {
        $query = DB::table('detail_linen')
            ->leftJoin('jenis_linen', 'jenis_linen.jenis_id', '=', 'detail_linen.detail_id_jenis')
            ->leftJoin('ruangan', 'ruangan.ruangan_id', '=', 'detail_linen.detail_id_ruangan')
            ->leftJoin('rs', 'rs.rs_id', '=', 'detail_linen.detail_id_rs')
            ->where('detail_linen.detail_status_linen', LinenStatusEnum::BERSIH)
            ->where(function ($q) use ($filter) {
                // shortcut: NULL = belum pernah di-report, anggap paling lama mengendap.
                $q->whereDate('detail_linen.detail_report', '<=', $this->cycleCutoff($filter))
                    ->orWhereNull('detail_linen.detail_report');
            });

        // ponytail: isi = guard akses + where; kosong = batasi ke RS milik user.
        $query = User::applyRsFilter($query, 'detail_linen.detail_id_rs', $filter['rs_id'] ?? null);

        return $query->select([
            'detail_linen.detail_rfid',
            'detail_linen.detail_report',
            'detail_linen.detail_total_bersih',
            'jenis_linen.jenis_nama as jenis_nama',
            'rs.rs_nama as rs_nama',
            'ruangan.ruangan_nama as ruangan_nama',
        ]);
    }
}
