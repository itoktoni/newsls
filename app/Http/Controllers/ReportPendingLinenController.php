<?php

namespace App\Http\Controllers;

use App\Concerns\ControllerTrait;
use App\Enums\TransactionType;
use App\Http\Requests\GeneralRequest;
use App\Models\Rs;
use App\Models\WebsiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Report Pending Dedicated — adopsi andalan ReportPendingLinenController.
 *
 * Sumber tabel `pending` (kotor → bersih tracking) + join master.
 * Status: Pending = bersih_at NULL, Bersih = bersih_at NOT NULL,
 * selainnya filter pending_transaksi (KOTOR/REJECT/REWASH).
 * Proteksi data besar: count dulu, > REPORT_CHUNK otomatis streaming Excel.
 *
 * Tanpa $this->model sehingga lolos authorize.
 */
class ReportPendingLinenController extends Controller
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

        $validated = $this->validatePending($request);
        $query = $this->pendingBaseQuery($validated);

        $threshold = (int) env('REPORT_CHUNK', 10000);
        if ($threshold > 0 && (clone $query)->count() > $threshold) {
            return $this->getExportExcel($request);
        }

        $data = (clone $query)->orderBy('pending.pending_kotor_at')->get();
        $rs = ! empty($validated['rs_id'])
            ? Rs::where('rs_id', $validated['rs_id'])->first()
            : null;

        return $this->views($this->template(), array_merge($this->share(), [
            'data' => $data,
            'rs' => $rs,
            'start' => $validated['start_pending'] ?? null,
            'end' => $validated['end_pending'] ?? null,
        ]));
    }

    public function getExportExcel(Request $request)
    {
        set_time_limit(0);

        $validated = $this->validatePending($request);

        $rs = ! empty($validated['rs_id'])
            ? Rs::where('rs_id', $validated['rs_id'])->first()
            : null;
        $rsNama = $rs->rs_nama ?? 'Semua Rumah Sakit';
        $logoUrl = WebsiteSetting::fileUrl(config('website.logo'));
        $logoAbs = $logoUrl ? url($logoUrl) : null;
        $periode = (formatDate($validated['start_pending'] ?? null) ?? '-')
            .' - '.(formatDate($validated['end_pending'] ?? null) ?? '-');

        $filename = 'pending-dedicated-'.now()->format('Ymd-His').'.xls';

        return response()->streamDownload(function () use ($validated, $rsNama, $logoAbs, $periode) {
            echo '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel">';
            echo '<head><meta charset="UTF-8"></head><body>';
            echo '<table><tr><td colspan="7"><b>REKAP PENDING DEDICATED</b><br><b>RUMAH SAKIT : '.e($rsNama).'</b><br><b>Periode : '.e($periode).'</b></td>';
            echo '<td colspan="2" style="text-align:right;">';
            if ($logoAbs) {
                echo '<img src="'.e($logoAbs).'" alt="Logo" height="60" width="90">';
            }
            echo '</td></tr></table><br>';
            echo '<table border="1"><thead><tr>'
                .'<th>No.</th><th>NO. RFID</th><th>LINEN</th><th>RUMAH SAKIT</th>'
                .'<th>RUANGAN</th><th>JUMLAH PEMAKAIAN</th><th>TRANSAKSI</th>'
                .'<th>TANGGAL KOTOR</th><th>TANGGAL BERSIH</th>'
                .'</tr></thead><tbody>';

            $no = 0;
            $this->pendingBaseQuery($validated)
                ->orderBy('pending.pending_kotor_at')
                ->chunk(1000, function ($rows) use (&$no) {
                    foreach ($rows as $table) {
                        $no++;
                        echo '<tr><td>'.$no.'</td>'
                            .'<td>'.e($table->pending_rfid).'</td>'
                            .'<td>'.e($table->jenis_nama ?? '-').'</td>'
                            .'<td>'.e($table->rs_nama ?? '-').'</td>'
                            .'<td>'.e($table->ruangan_nama ?? '-').'</td>'
                            .'<td>'.e($table->detail_total_bersih ?? 0).'</td>'
                            .'<td>'.e($table->pending_transaksi ?? '-').'</td>'
                            .'<td>'.e(formatDate($table->pending_kotor_at) ?? '-').'</td>'
                            .'<td>'.e(formatDate($table->pending_bersih_at) ?? '-').'</td></tr>';
                    }
                    flush();
                });

            echo '</tbody></table></body></html>';
        }, $filename, [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }

    protected function share($data = [])
    {
        $default = [
            'rsOptions' => \App\Models\User::rsOptions(),
            'statusOptions' => array_merge(TransactionType::getOptions(), ['Pending' => 'Pending']),
        ];

        return array_merge($default, $data);
    }

    private function validatePending(Request $request): array
    {
        return $request->validate([
            'rs_id' => 'nullable|integer|exists:rs,rs_id',
            'start_pending' => 'nullable|date',
            'end_pending' => 'nullable|date|after_or_equal:start_pending',
            'start_bersih' => 'nullable|date',
            'end_bersih' => 'nullable|date|after_or_equal:start_bersih',
            'status' => 'nullable|string',
        ]);
    }

    private function pendingBaseQuery(array $filter)
    {
        return DB::table('pending')
            ->leftJoin('rs', 'rs.rs_id', '=', 'pending.pending_id_rs')
            ->leftJoin('ruangan', 'ruangan.ruangan_id', '=', 'pending.pending_id_ruangan')
            ->leftJoin('jenis_linen', 'jenis_linen.jenis_id', '=', 'pending.pending_id_jenis')
            ->leftJoin('detail_linen', 'detail_linen.detail_rfid', '=', 'pending.pending_rfid')
            ->when(! empty($filter['rs_id']), fn ($q) => $q->where('pending.pending_id_rs', $filter['rs_id']))
            ->when(! empty($filter['start_pending']), fn ($q) => $q->whereDate('pending.pending_kotor_at', '>=', $filter['start_pending']))
            ->when(! empty($filter['end_pending']), fn ($q) => $q->whereDate('pending.pending_kotor_at', '<=', $filter['end_pending']))
            ->when(! empty($filter['start_bersih']), fn ($q) => $q->whereDate('pending.pending_bersih_at', '>=', $filter['start_bersih']))
            ->when(! empty($filter['end_bersih']), fn ($q) => $q->whereDate('pending.pending_bersih_at', '<=', $filter['end_bersih']))
            ->when(! empty($filter['status']), function ($q) use ($filter) {
                if ($filter['status'] === 'Pending') {
                    $q->whereNull('pending.pending_bersih_at');
                } elseif ($filter['status'] === 'BERSIH') {
                    $q->whereNotNull('pending.pending_bersih_at');
                } else {
                    $q->where('pending.pending_transaksi', $filter['status']);
                }
            })
            ->select([
                'pending.pending_rfid',
                'pending.pending_transaksi',
                'pending.pending_kotor_at',
                'pending.pending_bersih_at',
                'rs.rs_nama as rs_nama',
                'ruangan.ruangan_nama as ruangan_nama',
                'jenis_linen.jenis_nama as jenis_nama',
                'detail_linen.detail_total_bersih',
            ]);
    }
}
