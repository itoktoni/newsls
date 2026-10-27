<?php

namespace App\Http\Controllers\Dashboard;

use App\Charts\DashboardChart;
use App\Enums\LinenStatusEnum;
use App\Enums\RoleEnum;
use App\Enums\TransactionType;
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

        $payload = DashboardCache::remember($scope, 'admin:stats:v3:rs:'.($rsId ?? 'all'), function () use ($rsId) {
            $gudangId = Warehouse::utamaId();
            $rsCols = ['outstanding.outstanding_rs_scan', 'outstanding.outstanding_rs_ori'];

            $kpi = [
                'register' => User::applyRsFilter(DetailLinen::where('detail_status_linen', LinenStatusEnum::REGISTER), 'detail_linen.detail_id_rs', $rsId)->count(),
                'kotor' => User::applyRsFilter(DetailLinen::where('detail_status_linen', LinenStatusEnum::KOTOR), 'detail_linen.detail_id_rs', $rsId)->count(),
                // ponytail: pending = antrean laundry nyata (outstanding KOTOR/REJECT/REWASH),
                // bukan flag pending_created_at yang hanya untuk DEDICATED nyangkut >24 jam (selalu 0).
                'pending' => User::applyRsFilter(Outstanding::whereIn('outstanding_status_transaksi', [TransactionType::KOTOR, TransactionType::REJECT, TransactionType::RETUR, TransactionType::REWASH]), $rsCols, $rsId)->count(),
                'reject' => User::applyRsFilter(Outstanding::whereIn('outstanding_status_transaksi', [TransactionType::REJECT, TransactionType::RETUR]), $rsCols, $rsId)->count(),
                'rewash' => User::applyRsFilter(Outstanding::where('outstanding_status_transaksi', TransactionType::REWASH), $rsCols, $rsId)->count(),
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

            return compact('kpi', 'sebaran', 'health', 'opname');
        });

        return view('dashboard.admin', [
            'title' => 'Dashboard Admin',
            'kpi' => $payload['kpi'],
            'sebaran' => collect($payload['sebaran']),
            'health' => $payload['health'],
            'opname' => $payload['opname'],
            'rsOptions' => User::rsOptions(),
            'selectedRs' => $rsId,
            'userChart' => $chart->kotorVsBersih((int) config('dashboard.chart_days', 7), $rsId),
            'notifChart' => $chart->statusLinenDonut($rsId),
        ]);
    }
}
