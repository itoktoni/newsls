<?php

namespace App\Http\Controllers;

use App\Concerns\ControllerTrait;
use App\Enums\CuciEnum;
use App\Enums\LinenStatusEnum;
use App\Http\Requests\GeneralRequest;
use App\Models\JenisLinen;
use App\Models\Kategori;
use App\Models\Rs;
use App\Models\Ruangan;
use App\Models\User;
use App\Models\WebsiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Report Data Linen — adopsi andalan ReportDataLinenController.
 *
 * Data besar (ala andalan):
 * - set_time_limit(0) agar tidak timeout.
 * - getPrint tanpa limit — sebesar filter; bila baris melebihi REPORT_CHUNK
 *   (default 10000, = APP_CHUNK andalan) otomatis dialihkan ke streaming Excel.
 * - getExportExcel streaming per chunk 1000 + flush (tanpa ->get()),
 *   jadi memory tetap kecil berapa pun jumlah barisnya.
 *
 * Tanpa $this->model (seperti BersihController) sehingga lolos
 * GeneralRequest::authorize() dan tidak butuh Policy baru.
 */
class ReportDataLinenController extends Controller
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

        $query = $this->baseQuery($request);

        // Ala andalan: render HTML puluhan ribu baris bikin browser/PHP jebol,
        // jadi bila melebihi ambang langsung kirim sebagai streaming Excel.
        // Tanpa limit — batasnya = filter yang dipilih.
        $threshold = (int) env('REPORT_CHUNK', 10000);
        if ($threshold > 0 && (clone $query)->count() > $threshold) {
            return $this->getExportExcel($request);
        }

        $data = (clone $query)->orderBy('detail_linen.detail_rfid')->get();

        $rs = $request->filled('rs_id')
            ? Rs::where('rs_id', $request->input('rs_id'))->first()
            : null;

        return $this->views($this->template(), array_merge($this->share(), [
            'data' => $data,
            'rs' => $rs,
        ]));
    }

    /**
     * Export Excel via HTML table (dibuka langsung di Excel) —
     * streaming per chunk 1000 + flush ala andalan exportExcel(),
     * tanpa paket tambahan, tanpa limit — sebesar filter.
     */
    public function getExportExcel(Request $request)
    {
        set_time_limit(0);

        $rs = $request->filled('rs_id')
            ? Rs::where('rs_id', $request->input('rs_id'))->first()
            : null;
        $rsNama = $rs->rs_nama ?? 'Semua Rumah Sakit';
        $logoUrl = WebsiteSetting::fileUrl(config('website.logo'));
        $logoAbs = $logoUrl ? url($logoUrl) : null;

        $filename = 'report-data-linen-'.now()->format('Ymd-His').'.xls';

        return response()->streamDownload(function () use ($request, $rsNama, $logoAbs) {
            echo '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel">';
            echo '<head><meta charset="UTF-8"></head><body>';
            echo '<table><tr><td colspan="14"><b>MASTER DATA LINEN</b></td><td colspan="2">';
            if ($logoAbs) {
                echo '<img src="'.e($logoAbs).'" alt="Logo" height="60" width="90">';
            }
            echo '</td></tr>';
            echo '<tr><td colspan="16"><b>RUMAH SAKIT : '.e($rsNama).'</b></td></tr></table><br>';
            echo '<table border="1"><thead><tr>'
                .'<th>No.</th><th>NO. RFID</th><th>KATEGORI LINEN</th><th>LINEN</th>'
                .'<th>BERAT</th><th>RUMAH SAKIT</th><th>RUANGAN</th><th>CUCI/RENTAL</th>'
                .'<th>JUMLAH BERSIH</th><th>JUMLAH REJECT</th><th>JUMLAH REWASH</th>'
                .'<th>POSISI TERAKHIR</th><th>TGL POSISI TERAKHIR</th><th>STATUS REGISTER</th>'
                .'<th>TGL REGISTRASI</th><th>OPERATOR REGISTRASI</th>'
                .'</tr></thead><tbody>';

            $no = 0;
            $this->baseQuery($request)
                ->orderBy('detail_linen.detail_rfid')
                ->chunk(1000, function ($rows) use (&$no) {
                    foreach ($rows as $table) {
                        $no++;
                        echo '<tr><td>'.$no.'</td>'
                            .'<td>'.e($table->detail_rfid).'</td>'
                            .'<td>'.e($table->kategori_nama ?? '-').'</td>'
                            .'<td>'.e($table->jenis_nama ?? '-').'</td>'
                            .'<td>'.e($table->jenis_berat ?? '-').'</td>'
                            .'<td>'.e($table->rs_nama ?? '-').'</td>'
                            .'<td>'.e($table->ruangan_nama ?? '-').'</td>'
                            .'<td>'.e($table->detail_status_cuci ?? '-').'</td>'
                            .'<td>'.e($table->detail_total_bersih ?? 0).'</td>'
                            .'<td>'.e($table->detail_total_reject ?? 0).'</td>'
                            .'<td>'.e($table->detail_total_rewash ?? 0).'</td>'
                            .'<td>'.e($table->detail_status_linen ?? '-').'</td>'
                            .'<td>'.e(formatDate($table->detail_updated_at) ?? '-').'</td>'
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
            'kategoriOptions' => Kategori::orderBy('kategori_nama')->pluck('kategori_nama', 'kategori_id')->all(),
            'cuciOptions' => CuciEnum::getOptions(),
            'linenOptions' => LinenStatusEnum::getOptions(),
            // ponytail: peta RS → ruangan/jenis untuk dropdown dependen
            // (pivot rs_dan_ruangan / rs_dan_jenis), seperti DetailLinenController.
            'ruanganByRs' => DB::table('rs_dan_ruangan')->select('rs_id', 'ruangan_id')->get()
                ->groupBy('rs_id')->map(fn ($g) => $g->pluck('ruangan_id')->all())->all(),
            'jenisByRs' => DB::table('rs_dan_jenis')->select('rs_id', 'jenis_id')->get()
                ->groupBy('rs_id')->map(fn ($g) => $g->pluck('jenis_id')->all())->all(),
        ];

        return array_merge($default, $data);
    }

    private function baseQuery(Request $request)
    {
        $query = DB::table('detail_linen')
            ->leftJoin('rs', 'rs.rs_id', '=', 'detail_linen.detail_id_rs')
            ->leftJoin('ruangan', 'ruangan.ruangan_id', '=', 'detail_linen.detail_id_ruangan')
            ->leftJoin('jenis_linen', 'jenis_linen.jenis_id', '=', 'detail_linen.detail_id_jenis')
            ->leftJoin('kategori', 'kategori.kategori_id', '=', 'jenis_linen.jenis_id_kategori')
            ->leftJoin('users', 'users.id', '=', 'detail_linen.detail_created_by')
            ->select([
                'detail_linen.detail_rfid',
                'detail_linen.detail_status_cuci',
                'detail_linen.detail_status_register',
                'detail_linen.detail_status_linen',
                'detail_linen.detail_total_bersih',
                'detail_linen.detail_total_reject',
                'detail_linen.detail_total_rewash',
                'detail_linen.detail_created_at',
                'detail_linen.detail_updated_at',
                'rs.rs_nama as rs_nama',
                'ruangan.ruangan_nama as ruangan_nama',
                'jenis_linen.jenis_nama as jenis_nama',
                'jenis_linen.jenis_berat as jenis_berat',
                'kategori.kategori_nama as kategori_nama',
                'users.name as operator_nama',
            ]);

        if ($request->filled('rs_id')) {
            User::ensureRsAccess((int) $request->input('rs_id'));
            $query->where('detail_linen.detail_id_rs', $request->input('rs_id'));
        } else {
            $query = User::scopeRs($query, 'detail_linen.detail_id_rs');
        }

        if ($request->filled('ruangan_id')) {
            $query->where('detail_linen.detail_id_ruangan', $request->input('ruangan_id'));
        }

        if ($request->filled('jenis_id')) {
            $query->where('detail_linen.detail_id_jenis', $request->input('jenis_id'));
        }

        if ($request->filled('kategori_id')) {
            $query->where('jenis_linen.jenis_id_kategori', $request->input('kategori_id'));
        }

        if ($request->filled('status_linen')) {
            $query->where('detail_linen.detail_status_linen', $request->input('status_linen'));
        }

        if ($request->filled('status_cuci')) {
            $query->where('detail_linen.detail_status_cuci', $request->input('status_cuci'));
        }

        if ($request->filled('q')) {
            $q = strtolower((string) $request->input('q'));
            $query->whereRaw('LOWER(detail_linen.detail_rfid) LIKE ?', ['%'.$q.'%']);
        }

        return $query;
    }
}
