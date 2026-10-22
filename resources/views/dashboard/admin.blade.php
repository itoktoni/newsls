<?php
/** @var array $kpi */
/** @var \Illuminate\Support\Collection $sebaran */
/** @var array $health */
/** @var array $opname */
/** @var array $rsOptions */
/** @var int|null $selectedRs */
/** @var \ArielMejiaDev\LarapexCharts\LarapexChart $userChart */
/** @var \ArielMejiaDev\LarapexCharts\LarapexChart $notifChart */
/** @var string $title */
$badgeClass = fn (string $level) => match ($level) {
    'ok' => 'bg-green-100 text-green-800',
    'warn' => 'bg-amber-100 text-amber-800',
    default => 'bg-red-100 text-red-800',
};
?>

<x-layouts::app :title="$title">
    <div class="content mt-4 lg:mt-0 space-y-4">
        <x-breadcrumb :items="[
            ['url' => '/dashboard', 'label' => 'Home'],
            ['url' => '', 'label' => 'Dashboard Admin'],
        ]" />

        <div>
            <form method="GET" action="{{ route('dashboard.admin') }}" class="mb-3 flex items-center gap-2">
                <label for="sebaran-rs" class="text-sm font-medium text-on-surface-variant whitespace-nowrap">Rumah Sakit</label>
                <select id="sebaran-rs" name="rs_id" onchange="this.form.submit()"
                    class="w-full sm:max-w-xs text-sm border border-outline-variant rounded-lg px-3 py-2 bg-surface text-on-surface">
                    <option value="">Semua Rumah Sakit</option>
                    @foreach ($rsOptions as $id => $nama)
                        <option value="{{ $id }}" @selected((int) $selectedRs === (int) $id)>{{ $nama }}</option>
                    @endforeach
                </select>
            </form>
            <p class="font-label-caps text-label-caps text-on-surface-variant uppercase tracking-widest mb-2">KPI {{ $selectedRs ? ($rsOptions[$selectedRs] ?? '') : 'Global' }}</p>
            <x-stat-widget :items="[
                ['value' => number_format($kpi['kotor']), 'label' => 'Kotor', 'icon_name' => 'local_laundry_service', 'bg_color' => 'bg-warning/10', 'icon_color' => 'text-warning'],
                ['value' => number_format($kpi['warehouse']), 'label' => 'Gudang', 'icon_name' => 'warehouse', 'bg_color' => 'bg-warning/10', 'icon_color' => 'text-warning'],
                ['value' => number_format($kpi['bersih']), 'label' => 'Bersih', 'icon_name' => 'task_alt', 'bg_color' => 'bg-success/10', 'icon_color' => 'text-success'],
                ['value' => number_format($kpi['pending']), 'label' => 'Pending', 'icon_name' => 'pending_actions', 'bg_color' => 'bg-info/10', 'icon_color' => 'text-info'],
            ]" />
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            <x-card label="Sebaran Global (Bersih per Ruangan)" icon="grid_view" :noGrid="true">
                @if ($sebaran->isEmpty())
                    <p class="text-sm text-on-surface-variant py-4">Belum ada linen bersih tercatat.</p>
                @else
                    <ul class="space-y-2">
                        @foreach ($sebaran as $row)
                            <li class="flex items-center justify-between text-sm border-b border-outline-variant/50 pb-1.5">
                                <span class="text-on-surface">{{ $row['ruangan_nama'] }}</span>
                                <span class="flex items-center gap-2">
                                    <span class="font-semibold">{{ number_format($row['stok']) }}</span>
                                    <span class="text-[10px] px-2 py-0.5 rounded-full font-semibold {{ $badgeClass($row['level']) }}">
                                        {{ $row['level'] === 'ok' ? 'Aman' : ($row['level'] === 'warn' ? 'Tipis' : 'Kosong') }}
                                    </span>
                                </span>
                            </li>
                        @endforeach
                    </ul>
                    <div class="mt-3">
                        <a href="{{ route('detail-linen.getTable') }}" class="text-sm text-primary hover:underline">Lihat Data Linen →</a>
                    </div>
                @endif
            </x-card>

            <div class="space-y-4">
                <x-card label="Detail Linen" icon="inventory_2" :noGrid="true">
                    <div class="grid grid-cols-3 gap-3">
                        @foreach ([
                            'rs' => 'Rumah Sakit',
                            'jenis_linen' => 'Jenis',
                            'ruangan' => 'Ruangan',
                        ] as $key => $label)
                            <div class="border border-outline-variant rounded-xl p-3 bg-surface-container text-center">
                                <p class="text-[10px] text-on-surface-variant uppercase tracking-wide">{{ $label }}</p>
                                <p class="text-xl font-bold text-on-surface">{{ number_format($health[$key] ?? 0) }}</p>
                            </div>
                        @endforeach
                    </div>
                </x-card>

                <x-card label="Opname" icon="fact_check" :noGrid="true">
                    <div class="grid grid-cols-3 gap-3">
                        <div class="border border-outline-variant rounded-xl p-3 bg-surface-container text-center">
                            <p class="text-[10px] text-on-surface-variant uppercase tracking-wide">Total</p>
                            <p class="text-xl font-bold text-on-surface">{{ number_format($opname['total']) }}</p>
                        </div>
                        <div class="border border-outline-variant rounded-xl p-3 bg-surface-container text-center">
                            <p class="text-[10px] text-on-surface-variant uppercase tracking-wide">Selesai</p>
                            <p class="text-xl font-bold text-green-700">{{ number_format($opname['selesai']) }}</p>
                        </div>
                        <div class="border border-outline-variant rounded-xl p-3 bg-surface-container text-center">
                            <p class="text-[10px] text-on-surface-variant uppercase tracking-wide">Proses</p>
                            <p class="text-xl font-bold text-amber-700">{{ number_format($opname['proses']) }}</p>
                        </div>
                    </div>
                    <div class="mt-3">
                        <a href="{{ route('opname.getTable') }}" class="text-sm text-primary hover:underline">Lihat Opname →</a>
                    </div>
                </x-card>
            </div>
        </div>

        <x-card label="Dashboard Overview" icon="analytics" :noGrid="true">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-5">
                <div class="lg:col-span-2 min-w-0 overflow-hidden">
                    <div class="min-w-0">{!! $userChart->container() !!}</div>
                </div>
                <div class="min-w-0 overflow-hidden">
                    <div class="min-w-0">{!! $notifChart->container() !!}</div>
                </div>
            </div>
        </x-card>
    </div>

    @push('scripts')
        {!! $userChart->script() !!}
        {!! $notifChart->script() !!}
    @endpush
</x-layouts::app>
