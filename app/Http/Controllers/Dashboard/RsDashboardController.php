<?php

namespace App\Http\Controllers\Dashboard;

use App\Enums\LinenStatusEnum;
use App\Enums\RoleEnum;
use App\Http\Controllers\Controller;
use App\Models\DetailLinen;
use App\Models\Outstanding;
use App\Models\User;
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
     * @return array{kpi: array, sebaran: Collection, alert: Collection}
     */
    private function stats(): array
    {
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

                return (object) [
                    'ruangan_id' => $row->ruangan_id,
                    'ruangan_nama' => $row->ruangan_nama,
                    'stok' => $stok,
                    'par' => null,
                    'fill' => null,
                    'level' => $stok === 0 ? 'danger' : ($stok < 5 ? 'warn' : 'ok'),
                ];
            });

        $alert = $sebaran->filter(fn ($row) => $row->stok === 0);

        return compact('kpi', 'sebaran', 'alert');
    }
}
