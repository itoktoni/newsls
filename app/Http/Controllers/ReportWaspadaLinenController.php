<?php

namespace App\Http\Controllers;

use App\Concerns\ControllerTrait;
use App\Http\Requests\GeneralRequest;
use App\Models\JenisLinen;
use App\Models\Rs;
use App\Models\Ruangan;
use App\Models\User;
use App\Models\WebsiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Report Linen Waspada — pantauan pemakaian mendekati batas.
 *
 * Bahasa dijaga: hanya "Waspada" (kuning) dan "Prioritas" (merah).
 * Tanpa kata tua/rusak di judul, kolom, maupun kode.
 *
 * Logika tercepat: 2x if integer, tanpa bagi/koma.
 * - Prioritas: pakai >= maks
 * - Waspada: pakai >= maks - 20
 * - maks null/0 = 100 (data lama aman)
 */
class ReportWaspadaLinenController extends Controller
{
    use ControllerTrait;

    public function getTable(GeneralRequest $request)
    {
        return $this->views($this->template(), $this->share());
    }

    public function getPrint(Request $request)
    {
        set_time_limit(0);

        $query = $this->baseQuery($request);

        $threshold = (int) env('REPORT_CHUNK', 10000);
        if ($threshold > 0 && (clone $query)->count() > $threshold) {
            return $this->getExportExcel($request);
        }

        $data = (clone $query)->orderBy('detail_linen.detail_rfid')->get()->map(function ($row) {
            $row->maks_cuci = ($m = (int) ($row->maks_cuci ?? 0)) <= 0 ? 100 : $m;
            $pakai = (int) ($row->detail_total_bersih ?? 0);
            $row->tingkat = $pakai >= $row->maks_cuci ? 'Prioritas' : 'Waspada';
            $row->sisa = $row->maks_cuci - $pakai;

            return $row;
        });

        $rs = $request->filled('rs_id')
            ? Rs::where('rs_id', $request->input('rs_id'))->first()
            : null;

        return $this->views($this->template(), array_merge($this->share(), [
            'data' => $data,
            'rs' => $rs,
        ]));
    }

    public function getExportExcel(Request $request)
    {
        set_time_limit(0);

        $rs = $request->filled('rs_id')
            ? Rs::where('rs_id', $request->input('rs_id'))->first()
            : null;
        $rsNama = $rs->rs_nama ?? 'Semua Rumah Sakit';
        $logoUrl = WebsiteSetting::fileUrl(config('website.logo'));
        $logoAbs = $logoUrl ? url($logoUrl) : null;

        $filename = 'report-linen-waspada-'.now()->format('Ymd-His').'.xls';

        return response()->streamDownload(function () use ($request, $rsNama, $logoAbs) {
            echo '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel">';
            echo '<head><meta charset="UTF-8"></head><body>';
            echo '<table><tr><td colspan="9"><b>REPORT LINEN WASPADA</b></td><td colspan="2">';
            if ($logoAbs) {
                echo '<img src="'.e($logoAbs).'" alt="Logo" height="60" width="90">';
            }
            echo '</td></tr>';
            echo '<tr><td colspan="11"><b>RUMAH SAKIT : '.e($rsNama).'</b></td></tr></table><br>';
            echo '<table border="1"><thead><tr>'
                .'<th>No.</th><th>NO. RFID</th><th>RUMAH SAKIT</th><th>RUANGAN</th><th>LINEN</th>'
                .'<th>PEMAKAIAN</th><th>BATAS</th><th>SISA</th><th>TINGKAT PERHATIAN</th>'
                .'<th>POSISI TERAKHIR</th><th>TGL POSISI TERAKHIR</th>'
                .'</tr></thead><tbody>';

            $no = 0;
            $this->baseQuery($request)
                ->orderBy('detail_linen.detail_rfid')
                ->chunk(1000, function ($rows) use (&$no) {
                    foreach ($rows as $table) {
                        $no++;
                        $maks = (int) ($table->maks_cuci ?? 0);
                        if ($maks <= 0) {
                            $maks = 100;
                        }
                        $pakai = (int) ($table->detail_total_bersih ?? 0);
                        $tingkat = $pakai >= $maks ? 'Prioritas' : 'Waspada';
                        $sisa = $maks - $pakai;
                        echo '<tr><td>'.$no.'</td>'
                            .'<td>'.e($table->detail_rfid).'</td>'
                            .'<td>'.e($table->rs_nama ?? '-').'</td>'
                            .'<td>'.e($table->ruangan_nama ?? '-').'</td>'
                            .'<td>'.e($table->jenis_nama ?? '-').'</td>'
                            .'<td>'.e($pakai).'</td>'
                            .'<td>'.e($maks).'</td>'
                            .'<td>'.e($sisa).'</td>'
                            .'<td>'.e($tingkat).'</td>'
                            .'<td>'.e($table->detail_status_linen ?? '-').'</td>'
                            .'<td>'.e(formatDate($table->detail_updated_at) ?? '-').'</td></tr>';
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
            'rsOptions' => User::rsOptions(),
            'ruanganOptions' => Ruangan::orderBy('ruangan_nama')->pluck('ruangan_nama', 'ruangan_id')->all(),
            'jenisOptions' => JenisLinen::orderBy('jenis_nama')->pluck('jenis_nama', 'jenis_id')->all(),
            'tingkatOptions' => ['Waspada' => 'Waspada', 'Prioritas' => 'Prioritas'],
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
            ->select([
                'detail_linen.detail_rfid',
                'detail_linen.detail_status_linen',
                'detail_linen.detail_total_bersih',
                'detail_linen.detail_updated_at',
                'rs.rs_nama as rs_nama',
                'ruangan.ruangan_nama as ruangan_nama',
                'jenis_linen.jenis_nama as jenis_nama',
                'jenis_linen.jenis_max_cuci as maks_cuci',
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

        if ($request->filled('q')) {
            $q = strtolower((string) $request->input('q'));
            $query->whereRaw('LOWER(detail_linen.detail_rfid) LIKE ?', ['%'.$q.'%']);
        }

        // Hanya yang perlu perhatian: pakai >= maks - 20 (2x if integer di SQL).
        $query->whereRaw('COALESCE(detail_linen.detail_total_bersih, 0) >= (COALESCE(jenis_linen.jenis_max_cuci, 100) - 20)');

        if ($request->input('tingkat') === 'Prioritas') {
            $query->whereRaw('COALESCE(detail_linen.detail_total_bersih, 0) >= COALESCE(jenis_linen.jenis_max_cuci, 100)');
        } elseif ($request->input('tingkat') === 'Waspada') {
            $query->whereRaw('COALESCE(detail_linen.detail_total_bersih, 0) < COALESCE(jenis_linen.jenis_max_cuci, 100)');
        }

        return $query;
    }
}
