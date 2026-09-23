<?php

namespace App\Http\Controllers\Dashboard;

use App\Charts\DashboardChart;
use App\Enums\LinenStatusEnum;
use App\Enums\RoleEnum;
use App\Http\Controllers\Controller;
use App\Models\DetailLinen;
use App\Models\Outstanding;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class LaundryDashboardController extends Controller
{
    public function __invoke(Request $request, DashboardChart $chart): RedirectResponse|View
    {
        if ($request->user()?->role !== RoleEnum::LAUNDRY) {
            flash()->info('Halaman dashboard tidak sesuai peran Anda — dialihkan ke dashboard default.');

            return redirect()->route('dashboard');
        }

        $gudangId = Warehouse::utamaId();

        $kpi = [
            'register' => User::scopeRs(
                DetailLinen::query()->where('detail_status_linen', LinenStatusEnum::REGISTER),
                'detail_linen.detail_id_rs'
            )->count(),
            'kotor' => User::scopeRs(
                DetailLinen::query()->where('detail_status_linen', LinenStatusEnum::KOTOR),
                'detail_linen.detail_id_rs'
            )->count(),
            'pending' => User::scopeRs(
                Outstanding::query()->whereNotNull('outstanding_pending_created_at'),
                'outstanding.outstanding_rs_scan'
            )->count(),
            'bersih' => User::scopeRs(
                DetailLinen::query()->where('detail_status_linen', LinenStatusEnum::BERSIH),
                'detail_linen.detail_id_rs'
            )->count(),
            'delivered' => User::scopeRs(
                DB::table('bersih')->whereDate('bersih_created_at', today()),
                'bersih.bersih_id_rs'
            )->count(),
            'warehouse' => User::scopeRs(
                Outstanding::query()
                    ->where('outstanding_status_proses', 'GUDANG')
                    ->where('outstanding_id_warehouse', $gudangId),
                'outstanding.outstanding_rs_scan'
            )->count(),
        ];

        $antrean = [
            'packing' => User::scopeRs(
                Outstanding::whereIn('outstanding_status_proses', ['SCAN', 'QC', 'REGISTER', 'GUDANG']),
                'outstanding.outstanding_rs_scan'
            )->count(),
            'delivery' => User::scopeRs(
                Outstanding::query()->where('outstanding_status_proses', 'PACKING'),
                'outstanding.outstanding_rs_scan'
            )->count(),
        ];

        $gudangPerJenis = User::scopeRs(Outstanding::query(), 'outstanding.outstanding_rs_scan')
            ->leftJoin('detail_linen', 'detail_linen.detail_rfid', '=', 'outstanding.outstanding_rfid')
            ->leftJoin('jenis_linen', 'jenis_linen.jenis_id', '=', 'detail_linen.detail_id_jenis')
            ->where('outstanding.outstanding_status_proses', 'GUDANG')
            ->where('outstanding.outstanding_id_warehouse', $gudangId)
            ->selectRaw('COALESCE(jenis_linen.jenis_nama, ?) as nama, COUNT(*) as pcs', ['Tanpa Jenis'])
            ->groupBy('jenis_linen.jenis_nama')
            ->orderByDesc('pcs')
            ->limit((int) config('dashboard.top_ruangan', 12))
            ->get();

        return view('dashboard.laundry', [
            'title' => 'Dashboard Petugas Laundry',
            'kpi' => $kpi,
            'antrean' => $antrean,
            'gudangPerJenis' => $gudangPerJenis,
            'chart' => $chart->kotorVsBersih((int) config('dashboard.chart_days', 7)),
        ]);
    }
}
