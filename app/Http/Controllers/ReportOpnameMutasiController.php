<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\BuildsOpnameReports;
use App\Models\DetailLinen;
use App\Models\OpnameDetail;
use App\Models\Transaksi;
use Carbon\Carbon;
use Illuminate\Http\Request;

/**
 * Report Opname Mutasi — ringkasan mutasi per hari selama periode opname
 * (opname_mulai .. opname_selesai, termasuk hari tanpa data).
 *
 * Definisi tiap kolom:
 * - REGISTER SAAT SO          : jumlah linen terdaftar di opname ini (kolom konstan,
 *                               sama untuk semua baris).
 * - SCAN LINEN TERBACA DI RS  : opname_detail ketemu = 1 yang opname_detail_waktu
 *                               (waktu scan) jatuh pada hari itu.
 * - BELUM TERBACA DI LAUNDRY  : saldo berjalan. Hari pertama = REGISTER - SCAN - PROSES,
 *                               hari berikutnya = saldo hari sebelumnya - SCAN hari itu.
 * - LINEN MASIH DALAM PROSES  : sudah ketemu tapi prosesnya belum selesai
 *                               (opname_detail_proses masih SCAN/QC/PACKING/PENDING).
 * - TOTAL OPNAME              : baris opname yang sudah terdaftar (transaksi tidak null)
 *                               pada hari itu — total kolomnya = REGISTER.
 * - LINEN BERCHIP TIDAK TERINDENTIFIKASI : RFID yang dibaca (transaksi) pada hari itu
 *                               tapi tidak punya detail linen terdaftar.
 */
class ReportOpnameMutasiController extends Controller
{
    use BuildsOpnameReports;

    /** Proses yang dianggap belum selesai di laundry. */
    private const PROSES_BERJALAN = ['SCAN', 'QC', 'PACKING', 'PENDING'];

    public function getTable(Request $request)
    {
        $share = $this->reportShare();

        if ($request->filled('opname_id')) {
            $share = $this->mutasiData($request);
        }

        return view('pages.report-opname-mutasi.table', $share);
    }

    public function getPrint(Request $request)
    {
        set_time_limit(0);

        return view('pages.report-opname-mutasi.print', $this->mutasiData($request));
    }

    public function getExportExcel(Request $request)
    {
        set_time_limit(0);
        $data = $this->mutasiData($request);

        return $this->reportExport('pages.report-opname-mutasi.print', $data, 'opname-mutasi-'.now()->format('Ymd-His').'.xls');
    }

    protected function mutasiData(Request $request): array
    {
        $opname = $this->reportOpname($request);
        $mulai = Carbon::parse($opname->opname_mulai)->startOfDay();
        $selesai = Carbon::parse($opname->opname_selesai)->startOfDay();

        $details = OpnameDetail::query()
            ->where('opname_detail_id_opname', $opname->opname_id)
            ->get(['opname_detail_waktu', 'opname_detail_transaksi', 'opname_detail_proses', 'opname_detail_ketemu']);

        $register = $details->whereNotNull('opname_detail_transaksi')->count();
        $perHari = $details->groupBy(fn ($row) => $row->opname_detail_waktu
            ? Carbon::parse($row->opname_detail_waktu)->format('Y-m-d')
            : '');

        $berchip = Transaksi::query()
            ->whereBetween('transaksi_created_at', [$mulai, $selesai->copy()->endOfDay()])
            ->whereNotIn('transaksi_rfid', DetailLinen::query()->select('detail_rfid'))
            ->get(['transaksi_created_at'])
            ->groupBy(fn ($row) => Carbon::parse($row->transaksi_created_at)->format('Y-m-d'))
            ->map->count();

        $rows = [];
        $no = 1;
        $saldo = null;

        for ($hari = $mulai->copy(); $hari->lte($selesai); $hari->addDay()) {
            $key = $hari->format('Y-m-d');
            $harian = $perHari->get($key) ?? collect();

            $scan = $harian->where('opname_detail_ketemu', 1)->count();
            $proses = $harian->where('opname_detail_ketemu', 1)
                ->whereIn('opname_detail_proses', self::PROSES_BERJALAN)
                ->count();
            $total = $harian->whereNotNull('opname_detail_transaksi')->count();

            $saldo = $saldo === null ? $register - $scan - $proses : $saldo - $scan;

            $rows[] = [
                'no' => $no++,
                'tanggal' => $hari->format('d/m/Y'),
                'register' => $register,
                'scan' => $scan,
                'belum' => $saldo,
                'proses' => $proses,
                'total' => $total,
                'berchip' => (int) ($berchip[$key] ?? 0),
            ];
        }

        return $this->reportShare([
            'opname' => $opname,
            'rows' => $rows,
            'sum' => [
                'register' => $register,
                'scan' => array_sum(array_column($rows, 'scan')),
                'belum' => $saldo,
                'proses' => array_sum(array_column($rows, 'proses')),
                'total' => array_sum(array_column($rows, 'total')),
                'berchip' => array_sum(array_column($rows, 'berchip')),
            ],
        ]);
    }
}
