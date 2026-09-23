<?php

namespace App\Http\Controllers;

use App\Concerns\ControllerTrait;
use App\Http\Requests\GeneralRequest;
use App\Models\Rs;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Report Hilang Linen — adopsi andalan ReportHilangLinenController.
 *
 * Sumber `outstanding` status_hilang = HILANG + tanggal hilang.
 * Kolom: RFID, linen, RS, ruangan, jumlah pemakaian, tgl kotor,
 * lama hilang (hari), status, proses terakhir.
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

        $data = (clone $query)->orderBy('outstanding.outstanding_hilang_created_at')->get();
        $rs = ! empty($validated['rs_id'])
            ? Rs::where('rs_id', $validated['rs_id'])->first()
            : null;

        return $this->views($this->template(), array_merge($this->share(), [
            'data' => $data,
            'rs' => $rs,
            'start' => $validated['start_hilang'] ?? null,
            'end' => $validated['end_hilang'] ?? null,
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

        $filename = 'hilang-linen-'.now()->format('Ymd-His').'.xls';

        return [$filename, [
            'data' => $data,
            'rs' => $rs,
            'start' => $validated['start_hilang'] ?? null,
            'end' => $validated['end_hilang'] ?? null,
        ]];
    }

    protected function share($data = [])
    {
        $default = [
            'rsOptions' => \App\Models\User::rsOptions(),
        ];

        return array_merge($default, $data);
    }

    private function validateHilang(Request $request): array
    {
        return $request->validate([
            'rs_id' => 'nullable|integer|exists:rs,rs_id',
            'start_hilang' => 'nullable|date',
            'end_hilang' => 'nullable|date|after_or_equal:start_hilang',
        ]);
    }

    private function hilangBaseQuery(array $filter)
    {
        $query = DB::table('outstanding')
            ->leftJoin('detail_linen', 'detail_linen.detail_rfid', '=', 'outstanding.outstanding_rfid')
            ->leftJoin('jenis_linen', 'jenis_linen.jenis_id', '=', 'detail_linen.detail_id_jenis')
            ->leftJoin('ruangan', 'ruangan.ruangan_id', '=', 'outstanding.outstanding_id_ruangan')
            ->leftJoin('rs', 'rs.rs_id', '=', 'outstanding.outstanding_rs_ori')
            ->where('outstanding.outstanding_status_hilang', 'HILANG');

        // ponytail: isi = guard akses + where; kosong = batasi ke RS milik user.
        $query = User::applyRsFilter($query, 'outstanding.outstanding_rs_ori', $filter['rs_id'] ?? null);

        return $query
            ->when(! empty($filter['start_hilang']), fn ($q) => $q->whereDate('outstanding.outstanding_hilang_created_at', '>=', $filter['start_hilang']))
            ->when(! empty($filter['end_hilang']), fn ($q) => $q->whereDate('outstanding.outstanding_hilang_created_at', '<=', $filter['end_hilang']))
            ->select([
                'outstanding.outstanding_rfid',
                'outstanding.outstanding_created_at',
                'outstanding.outstanding_hilang_created_at',
                'outstanding.outstanding_status_transaksi',
                'outstanding.outstanding_status_proses',
                'detail_linen.detail_total_bersih',
                'jenis_linen.jenis_nama as jenis_nama',
                'rs.rs_nama as rs_nama',
                'ruangan.ruangan_nama as ruangan_nama',
            ]);
    }
}
