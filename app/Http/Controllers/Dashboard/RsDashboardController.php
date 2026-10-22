<?php

namespace App\Http\Controllers\Dashboard;

use App\Enums\LinenStatusEnum;
use App\Enums\RoleEnum;
use App\Http\Controllers\Controller;
use App\Models\DetailLinen;
use App\Models\Outstanding;
use App\Models\User;
use App\Support\DashboardCache;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class RsDashboardController extends Controller
{
    public function __invoke(Request $request): RedirectResponse|View
    {
        if ($request->user()?->role !== RoleEnum::RS) {
            flash()->info('Halaman dashboard tidak sesuai peran Anda — dialihkan ke dashboard default.');

            return redirect()->route('dashboard');
        }

        return view('dashboard.rs', [
            'title' => 'Dashboard Rumah Sakit',
            ...$this->stats(),
        ]);
    }

    /**
     * @return array{kpi: array, sebaran: Collection, alert: Collection, menipis: Collection}
     */
    private function stats(): array
    {
        $scope = DashboardCache::userScope();

        $payload = DashboardCache::remember($scope, 'rs:stats', function () {
            $bersihQuery = fn () => User::scopeRs(
                DetailLinen::query()->where('detail_status_linen', LinenStatusEnum::BERSIH),
                'detail_linen.detail_id_rs'
            );

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
                'bersih' => $bersihQuery()->count(),
            ];

            // Sebaran bersih per ruangan: stok absolut (par per-ruangan tidak ada di schema).
            $sebaran = $bersihQuery()
                ->join('ruangan', 'ruangan.ruangan_id', '=', 'detail_linen.detail_id_ruangan')
                ->selectRaw('detail_linen.detail_id_ruangan as ruangan_id, ruangan.ruangan_nama, COUNT(*) as stok')
                ->groupBy('detail_linen.detail_id_ruangan', 'ruangan.ruangan_nama')
                ->orderBy('stok')
                ->limit((int) config('dashboard.top_ruangan', 12))
                ->get()
                ->map(function ($row) {
                    $stok = (int) $row->stok;

                    return [
                        'ruangan_id' => $row->ruangan_id,
                        'ruangan_nama' => $row->ruangan_nama,
                        'stok' => $stok,
                        'par' => null,
                        'fill' => null,
                        'level' => $stok === 0 ? 'danger' : ($stok < 5 ? 'warn' : 'ok'),
                    ];
                })
                ->values();

            $alert = $sebaran->filter(fn ($row) => $row['stok'] === 0)->values();

            // ponytail: stok menipis per RS+jenis vs parstock — 1 query agregat,
            // 2x if integer di PHP (tanpa bagi). null parstock = tidak diawasi.
            $menipis = collect($this->menipisRows())->filter(fn ($r) => $r['level'] !== 'ok')->values();

            return [
                'kpi' => $kpi,
                'sebaran' => $sebaran->all(),
                'alert' => $alert->all(),
                'menipis' => $menipis->all(),
            ];
        });

        return [
            'kpi' => $payload['kpi'],
            'sebaran' => collect($payload['sebaran']),
            'alert' => collect($payload['alert']),
            'menipis' => collect($payload['menipis'] ?? []),
        ];
    }

    private function menipisRows(): array
    {
        $ids = User::allowedRsIds();
        $bersih = \Illuminate\Support\Facades\DB::table('detail_linen')
            ->where('detail_status_linen', LinenStatusEnum::BERSIH)
            ->select('detail_id_rs', 'detail_id_jenis', \Illuminate\Support\Facades\DB::raw('COUNT(*) as stok'))
            ->groupBy('detail_id_rs', 'detail_id_jenis');

        $query = \Illuminate\Support\Facades\DB::table('rs_dan_jenis')
            ->join('jenis_linen', 'jenis_linen.jenis_id', '=', 'rs_dan_jenis.jenis_id')
            ->join('rs', 'rs.rs_id', '=', 'rs_dan_jenis.rs_id')
            ->leftJoinSub($bersih, 'b', function ($join) {
                $join->on('b.detail_id_rs', '=', 'rs_dan_jenis.rs_id')
                    ->on('b.detail_id_jenis', '=', 'rs_dan_jenis.jenis_id');
            })
            ->whereNotNull('rs_dan_jenis.parstock')
            ->select([
                'rs_dan_jenis.rs_id',
                'rs.rs_nama',
                'rs_dan_jenis.jenis_id',
                'jenis_linen.jenis_nama',
                'rs_dan_jenis.parstock',
                \Illuminate\Support\Facades\DB::raw('COALESCE(b.stok, 0) as stok'),
            ]);

        if ($ids !== null) {
            $query->whereIn('rs_dan_jenis.rs_id', $ids);
        }

        return $query->orderBy('rs.rs_nama')->orderBy('jenis_linen.jenis_nama')->get()
            ->map(function ($row) {
                $stok = (int) $row->stok;
                $par = (int) $row->parstock;

                return [
                    'rs_id' => (int) $row->rs_id,
                    'rs_nama' => $row->rs_nama,
                    'jenis_id' => (int) $row->jenis_id,
                    'jenis_nama' => $row->jenis_nama,
                    'stok' => $stok,
                    'parstock' => $par,
                    'kurang' => $par - $stok,
                    'level' => $stok === 0 ? 'danger' : ($stok < $par ? 'warn' : 'ok'),
                ];
            })
            ->all();
    }
}
