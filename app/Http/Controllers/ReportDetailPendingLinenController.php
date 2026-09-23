<?php

namespace App\Http\Controllers;

use App\Concerns\ControllerTrait;
use App\Enums\TransactionType;
use App\Http\Requests\GeneralRequest;
use App\Models\Rs;
use App\Models\WebsiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Report Detail Pending — adopsi andalan ReportDetailPendingLinenController.
 *
 * Sumber `outstanding` status_hilang = PENDING + tanggal pending.
 * Kolom: RFID, linen, RS, ruangan, pemakaian, tgl kotor, lama pending,
 * status, proses terakhir.
 * Proteksi data besar: count dulu, > REPORT_CHUNK otomatis streaming Excel.
 *
 * Tanpa $this->model sehingga lolos authorize.
 */
class ReportDetailPendingLinenController extends Controller
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

        $data = (clone $query)->orderBy('outstanding.outstanding_pending_created_at')->get();
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

        $filename = 'detail-pending-'.now()->format('Ymd-His').'.xls';

        return response()->streamDownload(function () use ($validated, $rsNama, $logoAbs, $periode) {
            echo '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel">';
            echo '<head><meta charset="UTF-8"></head><body>';
            echo '<table><tr><td colspan="8"><b>DETAIL PENDING LINEN</b><br><b>RUMAH SAKIT : '.e($rsNama).'</b><br><b>Periode : '.e($periode).'</b></td>';
            echo '<td colspan="2" style="text-align:right;">';
            if ($logoAbs) {
                echo '<img src="'.e($logoAbs).'" alt="Logo" height="60" width="90">';
            }
            echo '</td></tr></table><br>';
            echo '<table border="1"><thead><tr>'
                .'<th>No.</th><th>NO. RFID</th><th>LINEN</th><th>RUMAH SAKIT</th>'
                .'<th>RUANGAN</th><th>JUMLAH PEMAKAIAN</th><th>TANGGAL KOTOR</th>'
                .'<th>LAMA PENDING</th><th>STATUS</th><th>PROSES TERAKHIR</th>'
                .'</tr></thead><tbody>';

            $no = 0;
            $this->pendingBaseQuery($validated)
                ->orderBy('outstanding.outstanding_pending_created_at')
                ->chunk(1000, function ($rows) use (&$no) {
                    foreach ($rows as $table) {
                        $no++;
                        echo '<tr><td>'.$no.'</td>'
                            .'<td>'.e($table->outstanding_rfid).'</td>'
                            .'<td>'.e($table->jenis_nama ?? '-').'</td>'
                            .'<td>'.e($table->rs_nama ?? '-').'</td>'
                            .'<td>'.e($table->ruangan_nama ?? '-').'</td>'
                            .'<td>'.e($table->detail_total_bersih ?? 0).'</td>'
                            .'<td>'.e(formatDate($table->outstanding_created_at) ?? '-').'</td>'
                            .'<td>'.e($this->lamaPending($table->outstanding_pending_created_at)).'</td>'
                            .'<td>'.e(TransactionType::getDescription($table->outstanding_status_transaksi ?? '') ?: ($table->outstanding_status_transaksi ?? '-')).'</td>'
                            .'<td>'.e($table->outstanding_status_proses ?? '-').'</td></tr>';
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
        ];

        return array_merge($default, $data);
    }

    private function validatePending(Request $request): array
    {
        return $request->validate([
            'rs_id' => 'nullable|integer|exists:rs,rs_id',
            'start_pending' => 'nullable|date',
            'end_pending' => 'nullable|date|after_or_equal:start_pending',
        ]);
    }

    private function pendingBaseQuery(array $filter)
    {
        return DB::table('outstanding')
            ->leftJoin('detail_linen', 'detail_linen.detail_rfid', '=', 'outstanding.outstanding_rfid')
            ->leftJoin('jenis_linen', 'jenis_linen.jenis_id', '=', 'detail_linen.detail_id_jenis')
            ->leftJoin('ruangan', 'ruangan.ruangan_id', '=', 'outstanding.outstanding_id_ruangan')
            ->leftJoin('rs', 'rs.rs_id', '=', 'outstanding.outstanding_rs_ori')
            ->where('outstanding.outstanding_status_hilang', 'PENDING')
            ->when(! empty($filter['rs_id']), fn ($q) => $q->where('outstanding.outstanding_rs_ori', $filter['rs_id']))
            ->when(! empty($filter['start_pending']), fn ($q) => $q->whereDate('outstanding.outstanding_pending_created_at', '>=', $filter['start_pending']))
            ->when(! empty($filter['end_pending']), fn ($q) => $q->whereDate('outstanding.outstanding_pending_created_at', '<=', $filter['end_pending']))
            ->select([
                'outstanding.outstanding_rfid',
                'outstanding.outstanding_created_at',
                'outstanding.outstanding_pending_created_at',
                'outstanding.outstanding_status_transaksi',
                'outstanding.outstanding_status_proses',
                'detail_linen.detail_total_bersih',
                'jenis_linen.jenis_nama as jenis_nama',
                'rs.rs_nama as rs_nama',
                'ruangan.ruangan_nama as ruangan_nama',
            ]);
    }

    private function lamaPending($value): string
    {
        if (empty($value)) {
            return '0 Hari';
        }

        try {
            return Carbon::parse($value)->diffInDays(now()).' Hari';
        } catch (\Throwable $e) {
            return '0 Hari';
        }
    }
}
