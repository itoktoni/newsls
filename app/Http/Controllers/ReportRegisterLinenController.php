<?php

namespace App\Http\Controllers;

use App\Concerns\ControllerTrait;
use App\Enums\CuciEnum;
use App\Enums\RegisterEnum;
use App\Http\Requests\GeneralRequest;
use App\Models\JenisLinen;
use App\Models\Rs;
use App\Models\Ruangan;
use App\Models\User;
use App\Models\WebsiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Report Register Linen — adopsi andalan ReportRegisterLinenController.
 *
 * List linen yang diregister (detail_created_at) + filter RS/jenis/
 * ruangan/cuci/status register. Proteksi data besar: count dulu,
 * > REPORT_CHUNK otomatis streaming Excel per chunk 1000.
 *
 * Tanpa $this->model sehingga lolos authorize.
 */
class ReportRegisterLinenController extends Controller
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

        $validated = $this->validateRegister($request);
        $query = $this->registerBaseQuery($validated);

        $threshold = (int) env('REPORT_CHUNK', 10000);
        if ($threshold > 0 && (clone $query)->count() > $threshold) {
            return $this->getExportExcel($request);
        }

        $data = (clone $query)->orderBy('detail_linen.detail_created_at')->get();
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

    public function getExportExcel(Request $request)
    {
        set_time_limit(0);

        $validated = $this->validateRegister($request);

        $rs = ! empty($validated['rs_id'])
            ? Rs::where('rs_id', $validated['rs_id'])->first()
            : null;
        $rsNama = $rs->rs_nama ?? 'Semua Rumah Sakit';
        $logoUrl = WebsiteSetting::fileUrl(config('website.logo'));
        $logoAbs = $logoUrl ? url($logoUrl) : null;
        $periode = (formatDate($validated['start_date'] ?? null) ?? '-')
            .' - '.(formatDate($validated['end_date'] ?? null) ?? '-');

        $filename = 'register-linen-'.now()->format('Ymd-His').'.xls';

        return response()->streamDownload(function () use ($validated, $rsNama, $logoAbs, $periode) {
            echo '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel">';
            echo '<head><meta charset="UTF-8"></head><body>';
            echo '<table><tr><td colspan="7"><b>DETAIL REGISTER LINEN</b><br><b>RUMAH SAKIT : '.e($rsNama).'</b><br><b>Periode : '.e($periode).'</b></td>';
            echo '<td colspan="2" style="text-align:right;">';
            if ($logoAbs) {
                echo '<img src="'.e($logoAbs).'" alt="Logo" height="60" width="90">';
            }
            echo '</td></tr></table><br>';
            echo '<table border="1"><thead><tr>'
                .'<th>No.</th><th>NO. RFID</th><th>LINEN</th><th>RUMAH SAKIT</th>'
                .'<th>RUANGAN</th><th>CUCI/RENTAL</th><th>STATUS REGISTRASI</th>'
                .'<th>TANGGAL REGISTER</th><th>OPERATOR</th>'
                .'</tr></thead><tbody>';

            $no = 0;
            $this->registerBaseQuery($validated)
                ->orderBy('detail_linen.detail_created_at')
                ->chunk(1000, function ($rows) use (&$no) {
                    foreach ($rows as $table) {
                        $no++;
                        echo '<tr><td>'.$no.'</td>'
                            .'<td>'.e($table->detail_rfid).'</td>'
                            .'<td>'.e($table->jenis_nama ?? '-').'</td>'
                            .'<td>'.e($table->rs_nama ?? '-').'</td>'
                            .'<td>'.e($table->ruangan_nama ?? '-').'</td>'
                            .'<td>'.e($table->detail_status_cuci ?? '-').'</td>'
                            .'<td>'.e($table->detail_status_register ?? '-').'</td>'
                            .'<td>'.e(formatDate($table->detail_created_at) ?? '-').'</td>'
                            .'<td>'.e($table->operator_nama ?? '-').'</td></tr>';
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
            'ruanganOptions' => Ruangan::orderBy('ruangan_nama')->pluck('ruangan_nama', 'ruangan_id')->all(),
            'jenisOptions' => JenisLinen::orderBy('jenis_nama')->pluck('jenis_nama', 'jenis_id')->all(),
            'cuciOptions' => CuciEnum::getOptions(),
            'registerOptions' => RegisterEnum::getOptions(),
            'ruanganByRs' => DB::table('rs_dan_ruangan')->select('rs_id', 'ruangan_id')->get()
                ->groupBy('rs_id')->map(fn ($g) => $g->pluck('ruangan_id')->all())->all(),
            'jenisByRs' => DB::table('rs_dan_jenis')->select('rs_id', 'jenis_id')->get()
                ->groupBy('rs_id')->map(fn ($g) => $g->pluck('jenis_id')->all())->all(),
        ];

        return array_merge($default, $data);
    }

    private function validateRegister(Request $request): array
    {
        return $request->validate([
            'rs_id' => 'nullable|integer|exists:rs,rs_id',
            'jenis_id' => 'nullable|integer|exists:jenis_linen,jenis_id',
            'ruangan_id' => 'nullable|integer|exists:ruangan,ruangan_id',
            'status_cuci' => 'nullable|string',
            'status_register' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);
    }

    /**
     * Ala andalan getConfig(): dari config_linen (pemilik MASTER) join
     * detail_linen — bukan detail_id_rs (pemegang sekarang). Linen GROUP
     * milik 2 RS tampil 2 baris (satu per pemilik), seperti view_config_linen.
     */
    private function registerBaseQuery(array $filter)
    {
        $query = DB::table('config_linen')
            ->join('detail_linen', 'detail_linen.detail_rfid', '=', 'config_linen.detail_rfid')
            ->leftJoin('jenis_linen', 'jenis_linen.jenis_id', '=', 'detail_linen.detail_id_jenis')
            ->leftJoin('ruangan', 'ruangan.ruangan_id', '=', 'detail_linen.detail_id_ruangan')
            ->leftJoin('rs', 'rs.rs_id', '=', 'config_linen.rs_id')
            ->leftJoin('users', 'users.id', '=', 'detail_linen.detail_created_by');

        // ponytail: isi = guard akses + where; kosong = batasi ke RS milik user.
        $query = User::applyRsFilter($query, 'config_linen.rs_id', $filter['rs_id'] ?? null);

        return $query
            ->when(! empty($filter['jenis_id']), fn ($q) => $q->where('detail_linen.detail_id_jenis', $filter['jenis_id']))
            ->when(! empty($filter['ruangan_id']), fn ($q) => $q->where('detail_linen.detail_id_ruangan', $filter['ruangan_id']))
            ->when(! empty($filter['status_cuci']), fn ($q) => $q->where('detail_linen.detail_status_cuci', $filter['status_cuci']))
            ->when(! empty($filter['status_register']), fn ($q) => $q->where('detail_linen.detail_status_register', $filter['status_register']))
            ->when(! empty($filter['start_date']), fn ($q) => $q->whereDate('detail_linen.detail_created_at', '>=', $filter['start_date']))
            ->when(! empty($filter['end_date']), fn ($q) => $q->whereDate('detail_linen.detail_created_at', '<=', $filter['end_date']))
            ->select([
                'detail_linen.detail_rfid',
                'detail_linen.detail_status_cuci',
                'detail_linen.detail_status_register',
                'detail_linen.detail_created_at',
                'jenis_linen.jenis_nama as jenis_nama',
                'rs.rs_nama as rs_nama',
                'ruangan.ruangan_nama as ruangan_nama',
                'users.name as operator_nama',
            ]);
    }
}
