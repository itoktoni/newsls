<?php
/** @var array $kpi */
/** @var \Illuminate\Support\Collection $sebaran */
/** @var array $health */
/** @var array $opname */
/** @var array $stats */
/** @var array $recentUsers */
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
        <div class="mb-2">
            <h2 class="text-2xl font-bold text-on-surface">{{ $title }}</h2>
        </div>

        <x-breadcrumb :items="[
            ['url' => route('dashboard.admin'), 'label' => 'Dashboard Admin'],
        ]" />

        <div>
            <p class="font-label-caps text-label-caps text-on-surface-variant uppercase tracking-widest mb-2">KPI Global</p>
            <x-stat-widget :items="[
                ['value' => number_format($kpi['register']), 'label' => 'Register', 'icon_name' => 'app_registration', 'bg_color' => 'bg-primary/10', 'icon_color' => 'text-primary'],
                ['value' => number_format($kpi['kotor']), 'label' => 'Kotor', 'icon_name' => 'local_laundry_service', 'bg_color' => 'bg-warning/10', 'icon_color' => 'text-warning'],
                ['value' => number_format($kpi['pending']), 'label' => 'Pending', 'icon_name' => 'pending_actions', 'bg_color' => 'bg-info/10', 'icon_color' => 'text-info'],
                ['value' => number_format($kpi['bersih']), 'label' => 'Bersih', 'icon_name' => 'task_alt', 'bg_color' => 'bg-success/10', 'icon_color' => 'text-success'],
                ['value' => number_format($kpi['outstanding']), 'label' => 'Outstanding', 'icon_name' => 'inventory', 'bg_color' => 'bg-primary/10', 'icon_color' => 'text-primary'],
                ['value' => number_format($kpi['warehouse']), 'label' => 'Gudang', 'icon_name' => 'warehouse', 'bg_color' => 'bg-warning/10', 'icon_color' => 'text-warning'],
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
                <x-card label="Data Kesehatan" icon="health_and_safety" :noGrid="true">
                    <div class="grid grid-cols-2 gap-3">
                        @foreach ([
                            'rs' => 'Rumah Sakit',
                            'jenis_linen' => 'Jenis Linen',
                            'config_linen' => 'Config Linen',
                            'outstanding' => 'Outstanding',
                            'pending' => 'Pending',
                        ] as $key => $label)
                            <div class="border border-outline-variant rounded-xl p-3 bg-surface-container">
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

        <x-card label="System Overview" icon="analytics" :noGrid="true">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                <div class="bg-surface-container rounded-xl p-4">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center">
                            <span class="material-symbols-outlined text-primary">people</span>
                        </div>
                        <span class="text-xs font-semibold text-on-surface-variant uppercase">Total Users</span>
                    </div>
                    <span class="text-2xl font-bold text-primary">{{ $stats['total_users'] }}</span>
                </div>
                <div class="bg-surface-container rounded-xl p-4">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="w-10 h-10 rounded-lg bg-info/10 flex items-center justify-center">
                            <span class="material-symbols-outlined text-info">notifications</span>
                        </div>
                        <span class="text-xs font-semibold text-on-surface-variant uppercase">Notifications</span>
                    </div>
                    <span class="text-2xl font-bold text-info">{{ $stats['total_notifications'] }}</span>
                </div>
                <div class="bg-surface-container rounded-xl p-4">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="w-10 h-10 rounded-lg bg-warning/10 flex items-center justify-center">
                            <span class="material-symbols-outlined text-warning">mark_email_unread</span>
                        </div>
                        <span class="text-xs font-semibold text-on-surface-variant uppercase">Unread</span>
                    </div>
                    <span class="text-2xl font-bold text-warning">{{ $stats['unread_notifications'] }}</span>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-5">
                <div class="lg:col-span-2 min-w-0 overflow-hidden">
                    <h4 class="font-semibold text-on-surface mb-2">Kotor vs Bersih (7 hari)</h4>
                    <div class="min-w-0">{!! $userChart->container() !!}</div>
                </div>
                <div class="min-w-0 overflow-hidden">
                    <h4 class="font-semibold text-on-surface mb-2">Status Linen</h4>
                    <div class="min-w-0">{!! $notifChart->container() !!}</div>
                </div>
            </div>

            <h4 class="font-semibold text-on-surface mb-2">Recent Users</h4>
            @if ($recentUsers->isNotEmpty())
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-left text-xs text-on-surface-variant uppercase border-b border-outline-variant">
                                <th class="pb-3 pr-4">Name</th>
                                <th class="pb-3 pr-4">Email</th>
                                <th class="pb-3 pr-4">Role</th>
                                <th class="pb-3">Joined</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($recentUsers as $user)
                                <tr class="border-b border-outline-variant/50">
                                    <td class="py-3 pr-4 font-medium">{{ $user['name'] }}</td>
                                    <td class="py-3 pr-4 text-on-surface-variant">{{ $user['email'] }}</td>
                                    <td class="py-3 pr-4">
                                        <span class="bg-surface-container text-xs px-2 py-1 rounded-full">{{ ucfirst($user['role']) }}</span>
                                    </td>
                                    <td class="py-3 text-on-surface-variant">{{ $user['created_at'] ?? '—' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-6 text-on-surface-variant text-sm">Belum ada user.</div>
            @endif
        </x-card>
    </div>

    @push('scripts')
        {!! $userChart->script() !!}
        {!! $notifChart->script() !!}
    @endpush
</x-layouts::app>
