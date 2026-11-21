<?php
/** @var array $kpi */
/** @var int|null $selectedRs */
/** @var array $rsOptions */
/** @var \Illuminate\Support\Collection $alert */
/** @var \ArielMejiaDev\LarapexCharts\LarapexChart $chart */
/** @var string $title */

use App\Enums\LinenStatusEnum;
?>

<x-layouts::app :title="$title">
    <div class="content mt-4 lg:mt-0 space-y-4">
        <div class="mb-2">
            <h2 class="text-2xl font-bold text-on-surface">{{ $title }}</h2>
        </div>

        <x-breadcrumb :items="[
            ['url' => route('dashboard.rs'), 'label' => 'Dashboard RS'],
        ]" />

        <form method="GET" action="{{ route('dashboard.rs') }}">
            <x-card label="Filter Rumah Sakit" icon="local_hospital">
                <x-select name="rs_id" label="Rumah Sakit" col="4" :options="$rsOptions" :default="$selectedRs" placeholder="-- Semua RS Saya --" />
            </x-card>
            <script>
                (function () {
                    var sel = document.querySelector('select[name="rs_id"]');
                    if (sel) sel.addEventListener('change', function () { this.form.submit(); });
                })();
            </script>
        </form>

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
                'badge' => formatQty($kpi['kotor_kg'] ?? 0).' kg',
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
                'badge' => formatQty($kpi['bersih_kg'] ?? 0).' kg',
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

        <x-chart-widget title="Kotor vs Bersih 7 Hari" :chart="$chart" />
    </div>
</x-layouts::app>
