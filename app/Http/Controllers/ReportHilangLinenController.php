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
 * Report Linen Stagnan di RS — alih fungsi dari Linen Hilang (status HILANG
 * tidak dipakai lagi karena linen disimpan di warehouse).
 *
 * Sumber `detail_linen` berstatus BERSIH (= fisik di rumah sakit) yang tidak
 * bergerak sejak cutoff: preset 1/2/3 bulan atau tanggal eksplisit
 * (default 1 bulan supaya tidak menumpahkan seluruh linen bersih).
 * Kolom: RFID, linen, RS, ruangan, jumlah pemakaian, update terakhir,
 * lama diam (hari).
 * Proteksi data besar: count dulu, > REPORT_CHUNK otomatis streaming Excel.
 *
 * Tanpa $this->model sehingga lolos authorize.
 */
class ReportHilangLinenController extends Controller
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

        $validated = $this->validateHilang($request);
        $query = $this->hilangBaseQuery($validated);

        $threshold = (int) env('REPORT_CHUNK', 10000);
        if ($threshold > 0 && (clone $query)->count() > $threshold) {
            return $this->getExportExcel($request);
        }

        $data = (clone $query)->orderBy('detail_linen.detail_updated_at')->get();
        $rs = ! empty($validated['rs_id'])
            ? Rs::where('rs_id', $validated['rs_id'])->first()
            : null;

        return $this->views($this->template(), array_merge($this->share(), [
            'data' => $data,
            'rs' => $rs,
            'start' => null,
            'end' => null,
            'stagnanLabel' => $this->stagnanLabel($validated),
        ]));
    }

    protected function excelPayload(Request $request): array
    {
        $validated = $this->validateHilang($request);

        $rs = ! empty($validated['rs_id'])
            ? Rs::where('rs_id', $validated['rs_id'])->first()
            : null;

        $data = $this->hilangBaseQuery($validated)
            ->orderBy('outstanding.outstanding_hilang_created_at')
            ->get();

        $filename = 'stagnan-rs-'.now()->format('Ymd-His').'.xls';

        return [$filename, [
            'data' => $data,
            'rs' => $rs,
            'start' => null,
            'end' => null,
            'stagnanLabel' => $this->stagnanLabel($validated),
        ]];
    }

    protected function share($data = [])
    {
        $default = [
            'rsOptions' => \App\Models\User::rsOptions(),
            'stagnanOptions' => ['1bulan' => '1 Bulan', '3bulan' => '3 Bulan', '6bulan' => '6 Bulan', '1tahun' => '> 1 Tahun'],
        ];

        return array_merge($default, $data);
    }

    private function validateHilang(Request $request): array
    {
        $validated = $request->validate([
            'rs_id' => 'nullable|integer|exists:rs,rs_id',
            'stagnan' => 'nullable|string|in:1bulan,3bulan,6bulan,1tahun',
            'stagnan_sejak' => 'nullable|date',
        ]);

        // Default 1 bulan supaya bukaan pertama tidak menumpahkan seluruh linen bersih.
        $validated['stagnan'] ??= empty($validated['stagnan_sejak']) ? '1bulan' : null;

        return $validated;
    }

    /**
     * Cutoff "tidak bergerak sejak": tanggal eksplisit menang atas preset.
     */
    private function stagnanCutoff(array $filter): string
    {
        if (! empty($filter['stagnan_sejak'])) {
            return $filter['stagnan_sejak'];
        }

        return match ($filter['stagnan'] ?? '1bulan') {
            '3bulan' => now()->subMonths(3)->format('Y-m-d'),
            '6bulan' => now()->subMonths(6)->format('Y-m-d'),
            '1tahun' => now()->subYear()->format('Y-m-d'),
            default => now()->subMonth()->format('Y-m-d'),
        };
    }

    private function stagnanLabel(array $filter): string
    {
        if (! empty($filter['stagnan_sejak'])) {
            return 'Tidak bergerak sejak '.formatDate($filter['stagnan_sejak']);
        }

        return match ($filter['stagnan'] ?? '1bulan') {
            '3bulan' => 'Tidak bergerak > 3 bulan',
            '6bulan' => 'Tidak bergerak > 6 bulan',
            '1tahun' => 'Tidak bergerak > 1 tahun',
            default => 'Tidak bergerak > 1 bulan',
        };
    }

    private function hilangBaseQuery(array $filter)
    {
        $query = DB::table('detail_linen')
            ->leftJoin('jenis_linen', 'jenis_linen.jenis_id', '=', 'detail_linen.detail_id_jenis')
            ->leftJoin('ruangan', 'ruangan.ruangan_id', '=', 'detail_linen.detail_id_ruangan')
            ->leftJoin('rs', 'rs.rs_id', '=', 'detail_linen.detail_id_rs')
            ->where('detail_linen.detail_status_linen', LinenStatusEnum::BERSIH)
            ->whereDate('detail_linen.detail_updated_at', '<=', $this->stagnanCutoff($filter));

        // ponytail: isi = guard akses + where; kosong = batasi ke RS milik user.
        $query = User::applyRsFilter($query, 'detail_linen.detail_id_rs', $filter['rs_id'] ?? null);

        return $query->select([
            'detail_linen.detail_rfid',
            'detail_linen.detail_updated_at',
            'detail_linen.detail_total_bersih',
            'jenis_linen.jenis_nama as jenis_nama',
            'rs.rs_nama as rs_nama',
            'ruangan.ruangan_nama as ruangan_nama',
        ]);
    }

    private function lamaDiam($value): string
    {
        if (empty($value)) {
            return '0 Hari';
        }

        try {
            return (int) floor(Carbon::parse($value)->diffInDays(now())).' Hari';
        } catch (\Throwable $e) {
            return '0 Hari';
        }
    }
}
