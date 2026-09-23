<?php
/** @var array $kpi */
/** @var array $antrean */
/** @var \Illuminate\Support\Collection $gudangPerJenis */
/** @var \ArielMejiaDev\LarapexCharts\LarapexChart $chart */
/** @var string $title */
?>

<x-layouts::app :title="$title">
    <div class="content mt-4 lg:mt-0 space-y-4">
        <div class="mb-2">
            <h2 class="text-2xl font-bold text-on-surface">{{ $title }}</h2>
        </div>

        <x-breadcrumb :items="[
            ['url' => route('dashboard.laundry'), 'label' => 'Dashboard Laundry'],
        ]" />

        <div>
            <p class="font-label-caps text-label-caps text-on-surface-variant uppercase tracking-widest mb-2">Pipeline</p>
            <x-stat-widget :items="[
                ['value' => number_format($kpi['register']), 'label' => 'Register', 'icon_name' => 'app_registration', 'bg_color' => 'bg-primary/10', 'icon_color' => 'text-primary'],
                ['value' => number_format($kpi['kotor']), 'label' => 'Kotor', 'icon_name' => 'local_laundry_service', 'bg_color' => 'bg-warning/10', 'icon_color' => 'text-warning'],
                ['value' => number_format($kpi['pending']), 'label' => 'Pending', 'icon_name' => 'pending_actions', 'bg_color' => 'bg-info/10', 'icon_color' => 'text-info'],
                ['value' => number_format($kpi['bersih']), 'label' => 'Bersih', 'icon_name' => 'task_alt', 'bg_color' => 'bg-success/10', 'icon_color' => 'text-success'],
                ['value' => number_format($kpi['delivered']), 'label' => 'Delivered Hari Ini', 'icon_name' => 'inventory_2', 'bg_color' => 'bg-success/10', 'icon_color' => 'text-success'],
                ['value' => number_format($kpi['warehouse']), 'label' => 'Warehouse', 'icon_name' => 'warehouse', 'bg_color' => 'bg-primary/10', 'icon_color' => 'text-primary'],
            ]" />
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            <x-card label="Antrean Packing / Siap Delivery" icon="local_shipping" :noGrid="true">
                <div class="grid grid-cols-2 gap-4">
                    <div class="border border-outline-variant rounded-xl p-4 bg-surface-container">
                        <p class="text-xs text-on-surface-variant uppercase tracking-wide">Antrean Packing</p>
                        <p class="text-3xl font-bold text-on-surface mt-1">{{ number_format($antrean['packing']) }}</p>
                        <p class="text-xs text-on-surface-variant mt-1">SCAN · QC · REGISTER · GUDANG</p>
                    </div>
                    <div class="border border-outline-variant rounded-xl p-4 bg-surface-container">
                        <p class="text-xs text-on-surface-variant uppercase tracking-wide">Siap Delivery</p>
                        <p class="text-3xl font-bold text-on-surface mt-1">{{ number_format($antrean['delivery']) }}</p>
                        <p class="text-xs text-on-surface-variant mt-1">Status PACKING</p>
                    </div>
                </div>
                <div class="mt-4">
                    <a href="{{ route('bersih.getTable') }}" class="text-sm text-primary hover:underline">Lihat menu Bersih →</a>
                </div>
            </x-card>

            <x-card label="Warehouse" icon="warehouse" :noGrid="true">
                @if ($gudangPerJenis->isEmpty())
                    <p class="text-sm text-on-surface-variant py-4">Gudang kosong.</p>
                @else
                    <ul class="space-y-2">
                        @foreach ($gudangPerJenis as $row)
                            <li class="flex items-center justify-between text-sm border-b border-outline-variant/50 pb-1.5">
                                <span class="text-on-surface">{{ $row['nama'] }}</span>
                                <span class="font-semibold text-on-surface">{{ number_format($row['pcs']) }} pcs</span>
                            </li>
                        @endforeach
                    </ul>
                    <div class="mt-4">
                        <a href="{{ route('warehouse.getTable') }}" class="text-sm text-primary hover:underline">Lihat Warehouse →</a>
                    </div>
                @endif
            </x-card>
        </div>

        <x-chart-widget title="Kotor vs Bersih 7 Hari" :chart="$chart" />

        @if ($kpi['register'] === 0 && $kpi['kotor'] === 0 && $kpi['bersih'] === 0 && $kpi['warehouse'] === 0)
            <p class="text-sm text-on-surface-variant text-center py-2">Belum ada data pipeline hari ini.</p>
        @endif
    </div>
</x-layouts::app>
