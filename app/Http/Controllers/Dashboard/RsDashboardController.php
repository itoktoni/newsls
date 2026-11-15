<?php

namespace App\Http\Controllers\Dashboard;

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

    public function __invoke(Request $request): RedirectResponse|View
    {
        if (static::defaultDashboardRoute($request->user()?->role) !== 'dashboard.rs') {
            flash()->info('Halaman dashboard tidak sesuai peran Anda — dialihkan ke dashboard default.');

            return redirect()->route('dashboard');
        }

        return view('dashboard.rs', [
            'title' => 'Dashboard Rumah Sakit',
            ...$this->stats(),
        ]);
    }

    /**
     * @return array{kpi: array, sebaran: Collection, alert: Collection}
     */
    private function stats(): array
    {
        $scope = DashboardCache::userScope();

        $payload = DashboardCache::remember($scope, 'rs:stats:v2', function () {
            // Kotor = baris transaksi KOTOR hari ini (sama dengan sumber
            // report kotor) — bukan status detail_linen.
            $kotorQuery = fn () => User::scopeRs(
                Transaksi::query()
                    ->where('transaksi.transaksi_status', TransactionType::KOTOR)
                    ->whereDate('transaksi.transaksi_created_at', today()),
                ['transaksi.transaksi_rs_ori', 'transaksi.transaksi_rs_scan']
            );
            // Bersih = baris tabel bersih hari ini (hasil delivery).
            $bersihQuery = fn () => User::scopeRs(
                DB::table('bersih')
                    ->where('bersih.bersih_status', 'BERSIH')
                    ->whereDate('bersih.bersih_created_at', today()),
                'bersih.bersih_id_rs'
            );

            $kpi = [
                'register' => User::scopeRs(
                    DetailLinen::query()->where('detail_status_linen', LinenStatusEnum::REGISTER),
                    'detail_linen.detail_id_rs'
                )->count(),
                'kotor' => $kotorQuery()->count(),
                'pending' => User::scopeRs(
                    Outstanding::query()->whereNotNull('outstanding_pending_created_at'),
                    'outstanding.outstanding_rs_scan'
                )->count(),
                'bersih' => $bersihQuery()->count(),
                'kotor_kg' => DashboardCache::sumBeratTransaksi($kotorQuery()),
                'bersih_kg' => DashboardCache::sumBeratBersih($bersihQuery()),
            ];

            // Sebaran = stok bersih per ruangan (posisi linen saat ini,
            // tetap dari detail_linen) — bukan arus delivery harian.
            $sebaran = User::scopeRs(
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
