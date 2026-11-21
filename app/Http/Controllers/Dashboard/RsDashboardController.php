<?php

namespace App\Http\Controllers\Dashboard;

use App\Charts\DashboardChart;
use App\Concerns\ResolvesDashboardRole;
use App\Enums\LinenStatusEnum;
use App\Enums\TransactionType;
use App\Http\Controllers\Controller;
use App\Models\DetailLinen;
use App\Models\Outstanding;
use App\Models\Transaksi;
use App\Models\User;
use App\Support\DashboardCache;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class RsDashboardController extends Controller
{
    use ResolvesDashboardRole;

    public function __invoke(Request $request, DashboardChart $chart): RedirectResponse|View
    {
        if (static::defaultDashboardRoute($request->user()?->role) !== 'dashboard.rs') {
            flash()->info('Halaman dashboard tidak sesuai peran Anda — dialihkan ke dashboard default.');

            return redirect()->route('dashboard');
        }

        // RS terpilih dari dropdown (query ?rs_id=). applyRsFilter sekaligus
        // guard: RS di luar daftar milik user → 403 via ensureRsAccess.
        $rsId = $request->filled('rs_id') ? (int) $request->input('rs_id') : null;
        if ($rsId !== null) {
            User::ensureRsAccess($rsId);
        }

        return view('dashboard.rs', [
            'title' => 'Dashboard Rumah Sakit',
            'rsOptions' => User::rsOptions(),
            'selectedRs' => $rsId,
            'chart' => $chart->kotorVsBersihRs($rsId, (int) config('dashboard.chart_days', 7)),
            ...$this->stats($rsId),
        ]);
    }

    /**
     * @return array{kpi: array, sebaran: Collection, alert: Collection}
     */
    private function stats(?int $rsId): array
    {
        $scope = DashboardCache::userScope();

        // Filter RS terpilih (null = semua RS milik user). Arrow fn
        // auto-capture $rsId; applyRsFilter sekaligus guard akses.
        $filter = fn ($query, string|array $columns) => User::applyRsFilter($query, $columns, $rsId);

        $payload = DashboardCache::remember($scope, 'rs:stats:v2:rs:'.($rsId ?? 'all'), function () use ($filter) {
            // Kotor = baris transaksi KOTOR hari ini (sama dengan sumber
            // report kotor) — bukan status detail_linen.
            $kotorQuery = fn () => $filter(
                Transaksi::query()
                    ->where('transaksi.transaksi_status', TransactionType::KOTOR)
                    ->whereDate('transaksi.transaksi_created_at', today()),
                ['transaksi.transaksi_rs_ori', 'transaksi.transaksi_rs_scan']
            );
            // Bersih = baris tabel bersih hari ini (hasil delivery).
            $bersihQuery = fn () => $filter(
                DB::table('bersih')
                    ->where('bersih.bersih_status', 'BERSIH')
                    ->whereDate('bersih.bersih_created_at', today()),
                'bersih.bersih_id_rs'
            );

            $kpi = [
                'register' => $filter(
                    DetailLinen::query()->where('detail_status_linen', LinenStatusEnum::REGISTER),
                    'detail_linen.detail_id_rs'
                )->count(),
                'kotor' => $kotorQuery()->count(),
                'pending' => $filter(
                    Outstanding::query()->whereNotNull('outstanding_pending_created_at'),
                    'outstanding.outstanding_rs_scan'
                )->count(),
                'bersih' => $bersihQuery()->count(),
                'kotor_kg' => DashboardCache::sumBeratTransaksi($kotorQuery()),
                'bersih_kg' => DashboardCache::sumBeratBersih($bersihQuery()),
            ];

            // Sebaran = stok bersih per ruangan (posisi linen saat ini,
            // tetap dari detail_linen) — bukan arus delivery harian.
            $sebaran = $filter(
                DetailLinen::query()->where('detail_status_linen', LinenStatusEnum::BERSIH),
                'detail_linen.detail_id_rs'
            )
                ->join('ruangan', 'ruangan.ruangan_id', '=', 'detail_linen.detail_id_ruangan')
                ->leftJoin('jenis_linen', 'jenis_linen.jenis_id', '=', 'detail_linen.detail_id_jenis')
                ->selectRaw('detail_linen.detail_id_ruangan as ruangan_id, ruangan.ruangan_nama, COUNT(*) as stok, SUM(jenis_linen.jenis_berat) as kg')
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
                        'kg' => (float) ($row->kg ?? 0),
                        'par' => null,
                        'fill' => null,
                        'level' => $stok === 0 ? 'danger' : ($stok < 5 ? 'warn' : 'ok'),
                    ];
                })
                ->values();

            $alert = $sebaran->filter(fn ($row) => $row['stok'] === 0)->values();

            return [
                'kpi' => $kpi,
                'sebaran' => $sebaran->all(),
                'alert' => $alert->all(),
            ];
        });

        return [
            'kpi' => $payload['kpi'],
            'sebaran' => collect($payload['sebaran']),
            'alert' => collect($payload['alert']),
        ];
    }
}
