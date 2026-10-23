<?php

namespace App\Http\Controllers;

use App\Concerns\ControllerTrait;
use App\Enums\TransactionType;
use App\Http\Requests\GeneralRequest;
use App\Models\Rs;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Report Selisih Berat — per kode transaksi master.
 *
 * Membandingkan BERAT KOTOR timbangan (master_qty, input desktop,
 * boleh kosong) vs BERAT BERSIH hitung live (Σ jenis_berat per RFID).
 * Filter RS + status + periode tanggal transaksi.
 *
 * Tanpa $this->model (seperti report lain) sehingga lolos
 * GeneralRequest::authorize() dan tidak butuh Policy baru.
 */
class ReportSelisihBeratController extends Controller
{
    use ControllerTrait;

    public function getTable(GeneralRequest $request)
    {
        return $this->views($this->template(), $this->share());
    }

    public function getPrint(Request $request)
    {
        set_time_limit(0);

        $validated = $this->validateSelisih($request);
        $data = $this->selisihRows($validated);
        $rs = Rs::where('rs_id', $validated['rs_id'])->firstOrFail();

        return $this->views($this->template(), array_merge($this->share(), [
            'title' => 'SELISIH BERAT TRANSAKSI',
            'data' => $data,
            'rs' => $rs,
            'status' => $validated['status'] ?? null,
            'start' => $validated['start_date'] ?? null,
            'end' => $validated['end_date'] ?? null,
            'total' => $this->sumTotal($data),
        ]));
    }

    public function getExportExcel(Request $request)
    {
        set_time_limit(0);

        $validated = $this->validateSelisih($request);
        $data = $this->selisihRows($validated);
        $rs = Rs::where('rs_id', $validated['rs_id'])->firstOrFail();

        $filename = 'selisih-berat-'.now()->format('Ymd-His').'.xls';

        return response()
            ->view($this->template('excel'), array_merge($this->share(), [
                'title' => 'SELISIH BERAT TRANSAKSI',
                'data' => $data,
                'rs' => $rs,
                'status' => $validated['status'] ?? null,
                'start' => $validated['start_date'] ?? null,
                'end' => $validated['end_date'] ?? null,
                'total' => $this->sumTotal($data),
            ]), 200, [
                'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="'.$filename.'"',
            ]);
    }

    protected function share($data = [])
    {
        $default = [
            'rsOptions' => User::rsOptions(),
            'statusOptions' => TransactionType::getOptions(),
        ];

        return array_merge($default, $data);
    }

    private function validateSelisih(Request $request): array
    {
        return $request->validate([
            'rs_id' => 'required|integer|exists:rs,rs_id',
            'status' => 'nullable|in:'.implode(',', [TransactionType::KOTOR, TransactionType::REJECT, TransactionType::REWASH]),
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);
    }

    /**
     * Baris per master + berat bersih live (SUM jenis_berat detail dalam
     * key yang sama) + selisih. Kolom bersih TIDAK disimpan di master.
     */
    private function selisihRows(array $filter)
    {
        $rows = $this->selisihQuery($filter)->get();

        if ($rows->isEmpty()) {
            return $rows;
        }

        $bersihMap = DB::table('transaksi')
            ->join('detail_linen', 'detail_linen.detail_rfid', '=', 'transaksi.transaksi_rfid')
            ->join('jenis_linen', 'jenis_linen.jenis_id', '=', 'detail_linen.detail_id_jenis')
            ->whereIn('transaksi.transaksi_key', $rows->pluck('master_kode')->all())
            ->groupBy('transaksi.transaksi_key')
            ->selectRaw('transaksi.transaksi_key as kode, SUM(jenis_linen.jenis_berat) as bersih')
            ->pluck('bersih', 'kode');

        return $rows->each(function ($row) use ($bersihMap) {
            $row->bersih = (float) ($bersihMap[$row->master_kode] ?? 0);
            $row->selisih = $row->master_qty !== null
                ? round((float) $row->master_qty - $row->bersih, 3)
                : null;
        });
    }

    private function selisihQuery(array $filter)
    {
        if (! Schema::hasTable('transaksi_master')) {
            return DB::table('transaksi_master')->whereRaw('1 = 0');
        }

        return DB::table('transaksi_master')
            ->leftJoin('rs', 'rs.rs_id', '=', 'transaksi_master.master_rs_id')
            ->leftJoin('users', 'users.id', '=', 'transaksi_master.master_created_by')
            ->where('transaksi_master.master_rs_id', $filter['rs_id'])
            ->when(! empty($filter['status']), fn ($q) => $q->where('transaksi_master.master_status', $filter['status']))
            ->when(! empty($filter['start_date']), fn ($q) => $q->whereDate('transaksi_master.master_tanggal', '>=', $filter['start_date']))
            ->when(! empty($filter['end_date']), fn ($q) => $q->whereDate('transaksi_master.master_tanggal', '<=', $filter['end_date']))
            ->orderBy('transaksi_master.master_tanggal')
            ->orderBy('transaksi_master.master_kode')
            ->select([
                'transaksi_master.master_kode',
                'transaksi_master.master_status',
                'transaksi_master.master_tanggal',
                'transaksi_master.master_qty',
                'rs.rs_nama as rs_nama',
                'users.name as operator_nama',
            ])
            ->selectRaw('(SELECT COUNT(*) FROM transaksi WHERE transaksi.transaksi_key = transaksi_master.master_kode) as n_rfid');
    }

    private function sumTotal($data): array
    {
        $kotor = 0.0;
        $bersih = 0.0;
        $nTimbang = 0;

        foreach ($data as $row) {
            $bersih += (float) ($row->bersih ?? 0);
            if ($row->master_qty !== null) {
                $kotor += (float) $row->master_qty;
                $nTimbang++;
            }
        }

        return [
            'n_transaksi' => $data->count(),
            'n_timbang' => $nTimbang,
            'kotor' => $nTimbang > 0 ? $kotor : null,
            'bersih' => $bersih,
            'selisih' => $nTimbang > 0 ? round($kotor - $bersih, 3) : null,
        ];
    }
}
