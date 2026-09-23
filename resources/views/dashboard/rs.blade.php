<?php
/** @var array $kpi */
/** @var \Illuminate\Support\Collection $sebaran */
/** @var \Illuminate\Support\Collection $alert */
/** @var string $title */

use App\Enums\LinenStatusEnum;

$badgeClass = fn (string $level) => match ($level) {
    'ok' => 'bg-green-100 text-green-800',
    'warn' => 'bg-amber-100 text-amber-800',
    default => 'bg-red-100 text-red-800',
};
?>

<x-layouts::app :title="$title">
    <div class="content mt-4 lg:mt-0 space-y-4">
        <div class="mb-2">
            <h2 class="text-2xl font-bold text-on-surface">{{ $title }}</h2>
        </div>

        <x-breadcrumb :items="[
            ['url' => route('dashboard.rs'), 'label' => 'Dashboard RS'],
        ]" />

        <x-stat-widget :items="[
            [
                'value' => number_format($kpi['register']),
                'label' => 'Register',
                'icon_name' => 'app_registration',
                'bg_color' => 'bg-primary/10',
                'icon_color' => 'text-primary',
            ],
            [
                'value' => number_format($kpi['kotor']),
                'label' => 'Kotor',
                'icon_name' => 'local_laundry_service',
                'bg_color' => 'bg-warning/10',
                'icon_color' => 'text-warning',
            ],
            [
                'value' => number_format($kpi['pending']),
                'label' => 'Pending',
                'icon_name' => 'pending_actions',
                'bg_color' => 'bg-info/10',
                'icon_color' => 'text-info',
            ],
            [
                'value' => number_format($kpi['bersih']),
                'label' => 'Bersih',
                'icon_name' => 'task_alt',
                'bg_color' => 'bg-success/10',
                'icon_color' => 'text-success',
            ],
        ]" />

        @if ($alert->isNotEmpty())
            <div class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-800" role="alert">
                <p class="font-semibold mb-1">Ruangan kurang stok</p>
                <ul class="list-disc pl-5 space-y-0.5">
                    @foreach ($alert as $row)
                        <li>{{ $row['ruangan_nama'] }} — stok 0</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <x-card label="Sebaran Linen Bersih per Ruangan" icon="grid_view">
            @if ($sebaran->isEmpty())
                <div class="col-span-12 py-8 text-center text-sm text-on-surface-variant">
                    Belum ada linen bersih tercatat.
                </div>
            @else
                <div class="col-span-12 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach ($sebaran as $row)
                        <div class="border border-outline-variant rounded-xl p-4 bg-surface-container">
                            <div class="flex items-start justify-between gap-2 mb-2">
                                <p class="font-medium text-on-surface text-sm">{{ $row['ruangan_nama'] }}</p>
                                <span class="text-[10px] px-2 py-0.5 rounded-full font-semibold {{ $badgeClass($row['level']) }}">
                                    {{ $row['level'] === 'ok' ? 'Aman' : ($row['level'] === 'warn' ? 'Tipis' : 'Kosong') }}
                                </span>
                            </div>
                            <p class="text-2xl font-bold text-on-surface">{{ number_format($row['stok']) }}</p>
                            <p class="text-xs text-on-surface-variant mt-0.5">Stok bersih · par —</p>
                        </div>
                    @endforeach
                </div>
                <div class="col-span-12 mt-2">
                    <a href="{{ route('detail-linen.getTable') }}" class="text-sm text-primary hover:underline">
                        Lihat Data Linen →
                    </a>
                </div>
            @endif
        </x-card>
    </div>
</x-layouts::app>
