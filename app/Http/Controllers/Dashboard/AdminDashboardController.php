<?php

namespace App\Http\Controllers\Dashboard;

use App\Charts\DashboardChart;
use App\Enums\LinenStatusEnum;
use App\Enums\RoleEnum;
use App\Http\Controllers\Controller;
use App\Models\DetailLinen;
use App\Models\JenisLinen;
use App\Models\Opname;
use App\Models\Outstanding;
use App\Models\Rs;
use App\Models\Ruangan;
use App\Models\User;
use App\Models\Warehouse;
use App\Support\DashboardCache;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function __invoke(Request $request, DashboardChart $chart): RedirectResponse|View
    {
        if (! in_array($request->user()?->role, [RoleEnum::ADMIN, RoleEnum::DEVELOPER], true)) {
            flash()->info('Halaman dashboard tidak sesuai peran Anda — dialihkan ke dashboard default.');

            return redirect()->route('dashboard');
        }

        $scope = DashboardCache::userScope();

        $rsId = $request->query('rs_id');
        $rsId = is_numeric($rsId) ? (int) $rsId : null;

        $payload = DashboardCache::remember($scope, 'admin:stats:v2:rs:'.($rsId ?? 'all'), function () use ($rsId) {
            $gudangId = Warehouse::utamaId();

            $kpi = [
                'register' => User::applyRsFilter(DetailLinen::where('detail_status_linen', LinenStatusEnum::REGISTER), 'detail_linen.detail_id_rs', $rsId)->count(),
                'kotor' => User::applyRsFilter(DetailLinen::where('detail_status_linen', LinenStatusEnum::KOTOR), 'detail_linen.detail_id_rs', $rsId)->count(),
                'pending' => User::applyRsFilter(Outstanding::whereNotNull('outstanding_pending_created_at'), ['outstanding.outstanding_rs_scan', 'outstanding.outstanding_rs_ori'], $rsId)->count(),
                'bersih' => User::applyRsFilter(DetailLinen::where('detail_status_linen', LinenStatusEnum::BERSIH), 'detail_linen.detail_id_rs', $rsId)->count(),
                'outstanding' => User::applyRsFilter(Outstanding::query(), ['outstanding.outstanding_rs_scan', 'outstanding.outstanding_rs_ori'], $rsId)->count(),
                'warehouse' => User::applyRsFilter(
                    Outstanding::where('outstanding_status_proses', 'GUDANG')->where('outstanding_id_warehouse', $gudangId),
                    ['outstanding.outstanding_rs_scan', 'outstanding.outstanding_rs_ori'],
                    $rsId
                )->count(),
            ];

            $sebaran = User::applyRsFilter(
                DetailLinen::query()->where('detail_status_linen', LinenStatusEnum::BERSIH),
                'detail_linen.detail_id_rs',
                $rsId
            )
                ->join('ruangan', 'ruangan.ruangan_id', '=', 'detail_linen.detail_id_ruangan')
                ->selectRaw('detail_linen.detail_id_ruangan as ruangan_id, ruangan.ruangan_nama, COUNT(*) as stok')
                ->groupBy('detail_linen.detail_id_ruangan', 'ruangan.ruangan_nama')
                ->orderBy('stok')
                ->limit((int) config('dashboard.top_ruangan', 12))
                ->get()
                ->map(fn ($row) => [
                    'ruangan_id' => $row->ruangan_id,
                    'ruangan_nama' => $row->ruangan_nama,
                    'stok' => (int) $row->stok,
                    'par' => null,
                    'level' => (int) $row->stok === 0 ? 'danger' : ((int) $row->stok < 5 ? 'warn' : 'ok'),
                ])
                ->all();

            $health = [
                'rs' => Rs::count(),
                'jenis_linen' => JenisLinen::count(),
                'ruangan' => Ruangan::count(),
            ];

            $opname = [
                'total' => Opname::count(),
                'selesai' => Opname::whereNotNull('opname_capture')->count(),
                'proses' => Opname::whereNull('opname_capture')->count(),
                'hilang_warehouse' => Opname::query()
                    ->whereHas('hasDetail', fn ($q) => $q->where('opname_detail_ketemu', 0))
                    ->count(),
            ];

            // Dashboard overview — recent users untuk tabel bawah.
            $recentUsers = User::latest()->limit(5)->get()
                ->map(fn ($user) => [
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                    'created_at' => $user->created_at?->format('d M Y'),
                ])
                ->all();

            return compact('kpi', 'sebaran', 'health', 'opname', 'recentUsers');
        });

        return view('dashboard.admin', [
            'title' => 'Dashboard Admin',
            'kpi' => $payload['kpi'],
            'sebaran' => collect($payload['sebaran']),
            'health' => $payload['health'],
            'opname' => $payload['opname'],
            'recentUsers' => collect($payload['recentUsers']),
            'rsOptions' => User::rsOptions(),
            'selectedRs' => $rsId,
            'userChart' => $chart->kotorVsBersih((int) config('dashboard.chart_days', 7), $rsId),
            'notifChart' => $chart->statusLinenDonut($rsId),
        ]);
    }
}
