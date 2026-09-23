<?php

namespace App\Http\Controllers\Dashboard;

use App\Charts\DashboardChart;
use App\Enums\LinenStatusEnum;
use App\Enums\RoleEnum;
use App\Http\Controllers\Controller;
use App\Models\DetailLinen;
use App\Models\JenisLinen;
use App\Models\Notification;
use App\Models\Opname;
use App\Models\Outstanding;
use App\Models\Rs;
use App\Models\User;
use App\Models\Warehouse;
use App\Support\DashboardCache;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

        $payload = DashboardCache::remember($scope, 'admin:stats', function () {
            $gudangId = Warehouse::utamaId();

            $kpi = [
                'register' => DetailLinen::where('detail_status_linen', LinenStatusEnum::REGISTER)->count(),
                'kotor' => DetailLinen::where('detail_status_linen', LinenStatusEnum::KOTOR)->count(),
                'pending' => Outstanding::whereNotNull('outstanding_pending_created_at')->count(),
                'bersih' => DetailLinen::where('detail_status_linen', LinenStatusEnum::BERSIH)->count(),
                'outstanding' => Outstanding::count(),
                'warehouse' => Outstanding::where('outstanding_status_proses', 'GUDANG')
                    ->where('outstanding_id_warehouse', $gudangId)
                    ->count(),
            ];

            $sebaran = DetailLinen::query()
                ->where('detail_status_linen', LinenStatusEnum::BERSIH)
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
                'config_linen' => DB::table('config_linen')->count(),
                'outstanding' => Outstanding::count(),
                'pending' => Outstanding::whereNotNull('outstanding_pending_created_at')->count(),
            ];

            $opname = [
                'total' => Opname::count(),
                'selesai' => Opname::whereNotNull('opname_capture')->count(),
                'proses' => Opname::whereNull('opname_capture')->count(),
                'hilang_warehouse' => Opname::query()
                    ->whereHas('hasDetail', fn ($q) => $q->where('opname_detail_ketemu', 0))
                    ->count(),
            ];

            // System overview — data lama dari DashboardController.
            $stats = [
                'total_users' => User::count(),
                'total_notifications' => Notification::count(),
                'unread_notifications' => Notification::where('read', false)->count(),
            ];
            $recentUsers = User::latest()->limit(5)->get()
                ->map(fn ($user) => [
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                    'created_at' => $user->created_at?->format('d M Y'),
                ])
                ->all();

            return compact('kpi', 'sebaran', 'health', 'opname', 'stats', 'recentUsers');
        });

        return view('dashboard.admin', [
            'title' => 'Dashboard Admin',
            'kpi' => $payload['kpi'],
            'sebaran' => collect($payload['sebaran']),
            'health' => $payload['health'],
            'opname' => $payload['opname'],
            'stats' => $payload['stats'],
            'recentUsers' => collect($payload['recentUsers']),
            'userChart' => $chart->kotorVsBersih((int) config('dashboard.chart_days', 7)),
            'notifChart' => $chart->statusLinenDonut(),
        ]);
    }
}
