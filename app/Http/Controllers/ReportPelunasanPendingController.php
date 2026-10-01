<?php

namespace App\Http\Controllers;

use App\Actions\PendingPelunasanAction;
use App\Concerns\ControllerTrait;
use App\Http\Requests\GeneralRequest;
use App\Models\Rs;
use App\Models\WebsiteSetting;
use Illuminate\Http\Request;

/**
 * Report Pelunasan Pending — lot hutang per tanggal vs pembayaran FIFO.
 *
 * Menjawab: lot kotor tanggal X sudah terbayar berapa, sisa berapa, dan
 * delivery mana yang membayarnya (RFID boleh berbeda, jenis harus sama).
 * Hitungan live via PendingPelunasanAction (tanpa tabel perantara).
 *
 * Tanpa $this->model sehingga lolos authorize.
 */
class ReportPelunasanPendingController extends Controller
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

        $validated = $this->validatePelunasan($request);
        $result = PendingPelunasanAction::run($validated);
        $rs = ! empty($validated['rs_id'])
            ? Rs::where('rs_id', $validated['rs_id'])->first()
            : null;

        return $this->views($this->template(), array_merge($this->share(), [
            'lots' => $result['lots'],
            'payments' => $result['payments'],
            'totalSisa' => $result['total_sisa'],
            'rs' => $rs,
            'start' => $validated['start'] ?? null,
            'end' => $validated['end'] ?? null,
        ]));
    }

    public function getExportExcel(Request $request)
    {
        set_time_limit(0);

        $validated = $this->validatePelunasan($request);
        $result = PendingPelunasanAction::run($validated);
        $rs = ! empty($validated['rs_id'])
            ? Rs::where('rs_id', $validated['rs_id'])->first()
            : null;
        $rsNama = $rs->rs_nama ?? 'Semua Rumah Sakit';
        $logoUrl = WebsiteSetting::fileUrl(config('website.logo'));
        $logoAbs = $logoUrl ? url($logoUrl) : null;
        $periode = (formatDate($validated['start'] ?? null) ?? '-')
            .' - '.(formatDate($validated['end'] ?? null) ?? '-');

        $filename = 'pelunasan-pending-'.now()->format('Ymd-His').'.xls';

        return response()->streamDownload(function () use ($result, $rsNama, $logoAbs, $periode) {
            echo '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel">';
            echo '<head><meta charset="UTF-8"></head><body>';
            echo '<table><tr><td colspan="6"><b>PELUNASAN PENDING</b><br><b>RUMAH SAKIT : '.e($rsNama).'</b><br><b>Periode Kotor : '.e($periode).'</b></td>';
            echo '<td colspan="2" style="text-align:right;">';
            if ($logoAbs) {
                echo '<img src="'.e($logoAbs).'" alt="Logo" height="60" width="90">';
            }
            echo '</td></tr></table><br>';

            echo '<table border="1"><thead><tr>'
                .'<th colspan="11">RINCIAN HUTANG (1 lot = 1 row per pembayaran)</th>'
                .'</tr><tr><th>No.</th><th>TGL KOTOR</th><th>RUMAH SAKIT</th><th>JENIS LINEN</th>'
                .'<th>STATUS</th><th>SISA AWAL</th><th>TGL BAYAR</th><th>KODE BAYAR</th><th>BAYAR</th><th>SISA</th><th>STATUS</th>'
                .'</tr></thead><tbody>';
            $no = 0;
            foreach ($result['lots'] as $lot) {
                $cicilan = $lot['cicilan'] ?: [['tanggal' => '-', 'code' => '-', 'qty' => 0, 'sisa_sebelum' => $lot['jumlah'], 'sisa_setelah' => $lot['sisa'], 'lunas_setelah' => false]];
                foreach ($cicilan as $c) {
                    $no++;
                    echo '<tr><td>'.$no.'</td>'
                        .'<td>'.e(formatDate($lot['tanggal']) ?? $lot['tanggal']).'</td>'
                        .'<td>'.e($lot['rs_nama']).'</td>'
                        .'<td>'.e($lot['jenis_nama']).'</td>'
                        .'<td>'.e($lot['status']).'</td>'
                        .'<td>'.e($c['sisa_sebelum'] ?? $lot['jumlah']).'</td>'
                        .'<td>'.e($c['tanggal'] === '-' ? '-' : (formatDate($c['tanggal']) ?? $c['tanggal'])).'</td>'
                        .'<td>'.e($c['code']).'</td>'
                        .'<td>'.e($c['qty']).'</td>'
                        .'<td>'.e($c['sisa_setelah'] ?? $lot['sisa']).'</td>'
                        .'<td>'.e(($c['lunas_setelah'] ?? $lot['lunas']) ? 'LUNAS' : 'BELUM').'</td></tr>';
                }
            }
            echo '</tbody></table><br>';

            echo '<table border="1"><thead><tr>'
                .'<th colspan="7">RINCIAN PEMBAYARAN (per delivery)</th>'
                .'</tr><tr><th>No.</th><th>KODE DELIVERY</th><th>TGL KIRIM</th><th>JENIS LINEN</th>'
                .'<th>JUMLAH</th><th>ALOKASI KE LOT</th><th>KELEBIHAN</th>'
                .'</tr></thead><tbody>';
            $no = 0;
            foreach ($result['payments'] as $pay) {
                $no++;
                $alokasi = collect($pay['alokasi'])->map(fn ($a) => $a['qty'].' untuk '.formatDate($a['lot_tanggal']))->implode('; ');
                echo '<tr><td>'.$no.'</td>'
                    .'<td>'.e($pay['code']).'</td>'
                    .'<td>'.e(formatDate($pay['tanggal']) ?? $pay['tanggal']).'</td>'
                    .'<td>'.e($pay['jenis_nama']).'</td>'
                    .'<td>'.e($pay['jumlah']).'</td>'
                    .'<td>'.e($alokasi ?: '-').'</td>'
                    .'<td>'.e($pay['kelebihan']).'</td></tr>';
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

    private function validatePelunasan(Request $request): array
    {
        $validated = $request->validate([
            'rs_id' => 'nullable|integer|exists:rs,rs_id',
            'start' => 'nullable|date',
            'end' => 'nullable|date|after_or_equal:start',
        ]);

        // Default 90 hari terakhir supaya bukaan pertama tidak men-scan setahun penuh.
        $validated['start'] ??= now()->subDays(90)->format('Y-m-d');

        return $validated;
    }
}
