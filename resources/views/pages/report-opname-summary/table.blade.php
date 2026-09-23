<x-layouts::app>
    <x-breadcrumb :items="[['url' => '/dashboard', 'label' => 'Home'], ['url' => '', 'label' => moduleLabel()]]" />
    <div class="content mt-4 lg:mt-0 space-y-4">
        @include('pages.report-rekap-opname._filter', [
            'title' => 'Report Opname Summary',
            'showJenis' => false,
        ])

        @isset($opname)
        @php
            $rows = $details->sortBy('opname_detail_waktu')->values();
            $map = $rows->mapToGroups(fn ($item) => [formatDate($item->opname_detail_waktu) => $item]);

            $tembakSo = fn ($list) => $list
                ->where('opname_detail_ketemu', 1)
                ->where('opname_detail_transaksi', '!=', 0)
                ->count();
            $hilangRs = fn ($list) => $list
                ->where('opname_detail_ketemu', 0)
                ->where('opname_detail_transaksi', \App\Enums\TransactionType::BERSIH)
                ->count();
            $hilangWh = fn ($list) => $list
                ->where('opname_detail_ketemu', 0)
                ->where('opname_detail_transaksi', '!=', \App\Enums\TransactionType::BERSIH)
                ->count();

            $subTembak = $tembakSo($rows);
            $subHilangRs = $hilangRs($rows);
            $subHilangWh = $hilangWh($rows);
            $registerCount = $register ?? $rows->whereNotNull('opname_detail_transaksi')->count();

            $grandTotal = 0;
            foreach ($map as $dayRows) {
                $grandTotal += $tembakSo($dayRows) + $hilangRs($dayRows) + $hilangWh($dayRows);
            }
        @endphp

        <div>
            <h2 class="text-sm font-bold uppercase tracking-wide text-on-surface-variant mb-2">Scan Linen Saat SO</h2>
            <x-table>
                <x-slot:head>
                    <th width="1">No.</th>
                    <th>TANGGAL</th>
                    <th>SCAN LINEN SAAT SO</th>
                </x-slot:head>

                <x-slot:body>
                    @forelse($map as $key => $dayRows)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $key ?? '' }}</td>
                        <td class="text-right font-semibold">{{ $tembakSo($dayRows) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="text-center text-on-surface-variant">Tidak ada data.</td>
                    </tr>
                    @endforelse
                    <tr class="bg-surface-container font-bold">
                        <td colspan="2">Total Snapshot</td>
                        <td class="text-right">{{ $subTembak }}</td>
                    </tr>
                </x-slot:body>

                <x-slot:mobile>
                    <div class="p-3 space-y-3">
                        @forelse($map as $key => $dayRows)
                        <div class="border border-outline-variant rounded-xl p-4 bg-surface-container-lowest shadow-sm">
                            <div class="flex items-center justify-between gap-3">
                                <div class="min-w-0">
                                    <p class="text-[10px] text-on-surface-variant uppercase tracking-wide">Hari ke-{{ $loop->iteration }}</p>
                                    <p class="text-sm font-bold text-on-surface truncate">{{ $key ?? '-' }}</p>
                                </div>
                                <p class="text-lg font-bold text-primary shrink-0">{{ $tembakSo($dayRows) }}</p>
                            </div>
                        </div>
                        @empty
                        <div class="border border-outline-variant rounded-xl p-4 text-sm text-on-surface-variant text-center">
                            Tidak ada data.
                        </div>
                        @endforelse
                        <div class="border border-outline-variant rounded-xl p-4 bg-surface-container shadow-sm flex items-center justify-between gap-3">
                            <p class="text-xs font-bold uppercase tracking-wide text-on-surface">Total Snapshot</p>
                            <p class="text-lg font-bold text-on-surface">{{ $subTembak }}</p>
                        </div>
                    </div>
                </x-slot:mobile>
            </x-table>
        </div>

        <div>
            <h2 class="text-sm font-bold uppercase tracking-wide text-on-surface-variant mb-2">Summary</h2>
            <x-table>
                <x-slot:head>
                    <th>Total Register</th>
                    <th>Total Scan Linen</th>
                    <th>Total belum terbaca di Rs</th>
                    <th>Total belum terbaca di Laundry</th>
                    <th>Total Summary</th>
                </x-slot:head>

                <x-slot:body>
                    <tr>
                        <td class="text-right font-semibold">{{ $registerCount }}</td>
                        <td class="text-right font-semibold">{{ $subTembak }}</td>
                        <td class="text-right font-semibold">{{ $subHilangRs }}</td>
                        <td class="text-right font-semibold">{{ $subHilangWh }}</td>
                        <td class="text-right font-bold">{{ $grandTotal }}</td>
                    </tr>
                </x-slot:body>

                <x-slot:mobile>
                    <div class="p-3">
                        <div class="grid grid-cols-2 gap-3">
                            <div class="border border-outline-variant rounded-xl p-4 bg-surface-container-lowest shadow-sm">
                                <p class="text-[10px] text-on-surface-variant uppercase tracking-wide mb-0.5">Total Register</p>
                                <p class="text-lg font-bold text-on-surface">{{ $registerCount }}</p>
                            </div>
                            <div class="border border-outline-variant rounded-xl p-4 bg-surface-container-lowest shadow-sm">
                                <p class="text-[10px] text-on-surface-variant uppercase tracking-wide mb-0.5">Total Scan Linen</p>
                                <p class="text-lg font-bold text-on-surface">{{ $subTembak }}</p>
                            </div>
                            <div class="border border-outline-variant rounded-xl p-4 bg-surface-container-lowest shadow-sm">
                                <p class="text-[10px] text-on-surface-variant uppercase tracking-wide mb-0.5">Belum terbaca di Rs</p>
                                <p class="text-lg font-bold text-on-surface">{{ $subHilangRs }}</p>
                            </div>
                            <div class="border border-outline-variant rounded-xl p-4 bg-surface-container-lowest shadow-sm">
                                <p class="text-[10px] text-on-surface-variant uppercase tracking-wide mb-0.5">Belum terbaca di Laundry</p>
                                <p class="text-lg font-bold text-on-surface">{{ $subHilangWh }}</p>
                            </div>
                        </div>
                        <div class="border border-outline-variant rounded-xl p-4 bg-surface-container mt-3 shadow-sm flex items-center justify-between gap-3">
                            <p class="text-xs font-bold uppercase tracking-wide text-on-surface">Total Summary</p>
                            <p class="text-xl font-bold text-primary">{{ $grandTotal }}</p>
                        </div>
                    </div>
                </x-slot:mobile>
            </x-table>
        </div>
        @endisset
    </div>
</x-layouts::app>
