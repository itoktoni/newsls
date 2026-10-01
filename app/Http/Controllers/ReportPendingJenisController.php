<?php

namespace App\Http\Controllers;

use App\Actions\PendingJenisRecapAction;
use App\Concerns\ControllerTrait;
use App\Http\Requests\GeneralRequest;
use App\Models\Rs;
use App\Models\WebsiteSetting;
use Illuminate\Http\Request;

/**
 * Report Pending per Jenis — hutang laundry agregat (bukan per RFID).
 *
 * MASUK = transaksi kotor/retur/rewash/register per jenis,
 * KELUAR = baris bersih yang sudah delivery, PENDING = selisihnya.
 * Pelunasan boleh memakai RFID berbeda asal jenisnya sama.
 * Hitungan live via PendingJenisRecapAction (tanpa tabel perantara).
 *
 * Tanpa $this->model sehingga lolos authorize.
 */
class ReportPendingJenisController extends Controller
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

        $validated = $this->validateRecap($request);
        $data = PendingJenisRecapAction::run($validated);
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

        $validated = $this->validateRecap($request);
        $data = PendingJenisRecapAction::run($validated);

        $rs = ! empty($validated['rs_id'])
            ? Rs::where('rs_id', $validated['rs_id'])->first()
            : null;
        $rsNama = $rs->rs_nama ?? 'Semua Rumah Sakit';
        $logoUrl = WebsiteSetting::fileUrl(config('website.logo'));
        $logoAbs = $logoUrl ? url($logoUrl) : null;
        $periode = (formatDate($validated['start'] ?? null) ?? '-')
            .' - '.(formatDate($validated['end'] ?? null) ?? '-');

        $filename = 'pending-per-jenis-'.now()->format('Ymd-His').'.xls';

        return response()->streamDownload(function () use ($data, $rsNama, $logoAbs, $periode) {
            echo '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel">';
            echo '<head><meta charset="UTF-8"></head><body>';
            echo '<table><tr><td colspan="5"><b>REKAP PENDING PER JENIS</b><br><b>RUMAH SAKIT : '.e($rsNama).'</b><br><b>Periode Kotor : '.e($periode).'</b></td>';
            echo '<td colspan="2" style="text-align:right;">';
            if ($logoAbs) {
                echo '<img src="'.e($logoAbs).'" alt="Logo" height="60" width="90">';
            }
            echo '</td></tr></table><br>';
            echo '<table border="1"><thead><tr>'
                .'<th>No.</th><th>RUMAH SAKIT</th><th>JENIS LINEN</th><th>STATUS</th>'
                .'<th>MASUK (KOTOR)</th><th>KELUAR (BERSIH)</th><th>PENDING</th>'
                .'</tr></thead><tbody>';

            $no = 0;
            foreach ($data as $table) {
                $no++;
                echo '<tr><td>'.$no.'</td>'
                    .'<td>'.e($table['rs_nama']).'</td>'
                    .'<td>'.e($table['jenis_nama']).'</td>'
                    .'<td>'.e($table['status']).'</td>'
                    .'<td>'.e($table['masuk']).'</td>'
                    .'<td>'.e($table['keluar']).'</td>'
                    .'<td>'.e($table['pending']).'</td></tr>';
            }

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

    private function validateRecap(Request $request): array
    {
        $validated = $request->validate([
            'rs_id' => 'nullable|integer|exists:rs,rs_id',
            'start' => 'nullable|date',
            'end' => 'nullable|date|after_or_equal:start',
        ]);

        // Default 90 hari terakhir supaya bukaan pertama tidak men-scan
        // setahun penuh (anti-keos). Kirim eksplisit untuk hasil all-time.
        $validated['start'] ??= now()->subDays(90)->format('Y-m-d');

        return $validated;
    }
}
