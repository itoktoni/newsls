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
 * Report Detail Packing — per RFID yang sedang di PACKING (outstanding).
 *
 * Sumber: `outstanding` dengan outstanding_status_proses = PACKING.
 * Kolom: RFID, linen, RS, ruangan, tgl kotor, tgl packing (update),
 * lama packing, status, proses.
 *
 * Tanpa $this->model sehingga lolos authorize.
 */
class ReportDetailPackingController extends Controller
{
    use ControllerTrait;

    public function getTable(GeneralRequest $request)
    {
        return $this->views($this->template(), $this->share());
    }

    public function getPrint(Request $request)
    {
        set_time_limit(0);

        $validated = $this->validatePacking($request);
        $query = $this->packingBaseQuery($validated);

        $threshold = (int) env('REPORT_CHUNK', 10000);
        if ($threshold > 0 && (clone $query)->count() > $threshold) {
            return $this->getExportExcel($request);
        }

        $data = (clone $query)->orderBy('jenis_linen.jenis_nama')->orderBy('outstanding.outstanding_updated_at')->get();
        $rs = ! empty($validated['rs_id'])
            ? Rs::where('rs_id', $validated['rs_id'])->first()
            : null;

        return $this->views($this->template(), array_merge($this->share(), [
            'data' => $data,
            'rs' => $rs,
            'start' => $validated['start'] ?? null,
            'end' => $validated['end'] ?? null,
        ]));
    }

    public function getExportExcel(Request $request)
    {
        set_time_limit(0);

        $validated = $this->validatePacking($request);

        $rs = ! empty($validated['rs_id'])
            ? Rs::where('rs_id', $validated['rs_id'])->first()
            : null;
        $rsNama = $rs->rs_nama ?? 'Semua Rumah Sakit';
        $logoUrl = WebsiteSetting::fileUrl(config('website.logo'));
        $logoAbs = $logoUrl ? url($logoUrl) : null;
        $periode = (formatDate($validated['start'] ?? null) ?? '-')
            .' - '.(formatDate($validated['end'] ?? null) ?? '-');

        $filename = 'detail-packing-'.now()->format('Ymd-His').'.xls';

        return response()->streamDownload(function () use ($validated, $rsNama, $logoAbs, $periode) {
            echo '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel">';
            echo '<head><meta charset="UTF-8"></head><body>';
            echo '<table><tr><td colspan="8"><b>DETAIL PACKING</b><br><b>RUMAH SAKIT : '.e($rsNama).'</b><br><b>Periode Packing : '.e($periode).'</b></td>';
            echo '<td colspan="2" style="text-align:right;">';
            if ($logoAbs) {
                echo '<img src="'.e($logoAbs).'" alt="Logo" height="60" width="90">';
            }
            echo '</td></tr></table><br>';
            echo '<table border="1"><thead><tr>'
                .'<th>No.</th><th>NO. RFID</th><th>LINEN</th><th>RUMAH SAKIT</th>'
                .'<th>RUANGAN</th><th>TANGGAL KOTOR</th><th>TANGGAL PACKING</th>'
                .'<th>LAMA PACKING</th><th>STATUS</th><th>PROSES</th>'
                .'</tr></thead><tbody>';

            $no = 0;
            $this->packingBaseQuery($validated)
                ->orderBy('jenis_linen.jenis_nama')
                ->orderBy('outstanding.outstanding_updated_at')
                ->chunk(1000, function ($rows) use (&$no) {
                    foreach ($rows as $table) {
                        $no++;
                        echo '<tr><td>'.$no.'</td>'
                            .'<td>'.e($table->outstanding_rfid).'</td>'
                            .'<td>'.e($table->jenis_nama ?? '-').'</td>'
                            .'<td>'.e($table->rs_nama ?? '-').'</td>'
                            .'<td>'.e($table->ruangan_nama ?? '-').'</td>'
                            .'<td>'.e(formatDate($table->outstanding_created_at) ?? '-').'</td>'
                            .'<td>'.e(formatDate($table->outstanding_updated_at) ?? '-').'</td>'
                            .'<td>'.e($this->lamaPacking($table->outstanding_updated_at)).'</td>'
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
        return array_merge(['rsOptions' => \App\Models\User::rsOptions()], $data);
    }

    private function validatePacking(Request $request): array
    {
        return $request->validate([
            'rs_id' => 'nullable|integer|exists:rs,rs_id',
            'start' => 'nullable|date',
            'end' => 'nullable|date|after_or_equal:start',
        ]);
    }

    private function packingBaseQuery(array $filter)
    {
        // RS diambil dari outstanding_rs_scan: packing mengupdate rs_scan
        // (PackingDeliveryController::updateOutstandingToPacking), bukan rs_ori.
        return DB::table('outstanding')
            ->leftJoin('detail_linen', 'detail_linen.detail_rfid', '=', 'outstanding.outstanding_rfid')
            ->leftJoin('jenis_linen', 'jenis_linen.jenis_id', '=', 'detail_linen.detail_id_jenis')
            ->leftJoin('ruangan', 'ruangan.ruangan_id', '=', 'outstanding.outstanding_id_ruangan')
            ->leftJoin('rs', 'rs.rs_id', '=', 'outstanding.outstanding_rs_scan')
            ->where('outstanding.outstanding_status_proses', 'PACKING')
            ->when(! empty($filter['rs_id']), fn ($q) => $q->where('outstanding.outstanding_rs_scan', $filter['rs_id']))
            ->when(! empty($filter['start']), fn ($q) => $q->whereDate('outstanding.outstanding_updated_at', '>=', $filter['start']))
            ->when(! empty($filter['end']), fn ($q) => $q->whereDate('outstanding.outstanding_updated_at', '<=', $filter['end']))
            ->select([
                'outstanding.outstanding_rfid',
                'outstanding.outstanding_created_at',
                'outstanding.outstanding_updated_at',
                'outstanding.outstanding_status_transaksi',
                'outstanding.outstanding_status_proses',
                'jenis_linen.jenis_nama as jenis_nama',
                'rs.rs_nama as rs_nama',
                'ruangan.ruangan_nama as ruangan_nama',
            ]);
    }

    private function lamaPacking($updatedAt = null): string
    {
        if (empty($updatedAt)) {
            return '0 Hari';
        }

        try {
            return (int) floor(Carbon::parse($updatedAt)->diffInDays(now())).' Hari';
        } catch (\Throwable $e) {
            return '0 Hari';
        }
    }
}
