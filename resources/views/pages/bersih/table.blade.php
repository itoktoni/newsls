<?php
/** @var string $tab */
// ponytail: asal transaksi RFID — KOTOR/REJECT(RE TUR)/REWASH/REGISTER.
// REJECT = hasil scan "retur" dari desktop (TransaksiApiController map RETUR→REJECT).
$asalLabel = fn ($v) => $v === 'REGISTER' ? 'Register' : \App\Enums\TransactionType::getDescription($v ?? '');
$asalBadge = fn ($v) => match ($v) {
    'KOTOR' => 'bg-amber-100 text-amber-800',
    'REJECT', 'RETUR' => 'bg-red-100 text-red-800',
    'REWASH' => 'bg-blue-100 text-blue-800',
    'REGISTER' => 'bg-gray-100 text-gray-800',
    default => 'bg-gray-100 text-gray-800',
};
?>

<x-layouts::app>
    <x-breadcrumb :items="[['url' => '/dashboard', 'label' => 'Home'], ['url' => '', 'label' => moduleLabel()]]" />
    <div class="content mt-4 lg:mt-0 space-y-4">

        {{-- Statistik --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <x-card :label="'Antrean Packing'" :noGrid="true">
                <div class="text-center py-2">
                    <p class="text-3xl font-bold text-amber-600">{{ $stats['antrean_packing'] ?? 0 }}</p>
                    <p class="text-xs text-on-surface-variant mt-1">RFID di laundry (SCAN/QC) menunggu packing</p>
                </div>
            </x-card>
            <x-card :label="'Siap Delivery'" :noGrid="true">
                <div class="text-center py-2">
                    <p class="text-3xl font-bold text-blue-600">{{ $stats['siap_delivery'] ?? 0 }}</p>
                    <p class="text-xs text-on-surface-variant mt-1">RFID sudah PACKING menunggu delivery</p>
                </div>
            </x-card>
            <x-card :label="'Bersih Hari Ini'" :noGrid="true">
                <div class="text-center py-2">
                    <p class="text-3xl font-bold text-green-600">{{ $stats['bersih_hari_ini'] ?? 0 }}</p>
                    <p class="text-xs text-on-surface-variant mt-1">Linen menjadi bersih hari ini</p>
                </div>
            </x-card>
        </div>

        {{-- Tabs --}}
        <div class="flex gap-2">
            <a href="{{ moduleRoute('getTable', ['tab' => 'packing']) }}" wire:navigate
               class="px-4 py-2 rounded-lg text-sm font-semibold {{ $tab === 'packing' ? 'bg-primary text-white' : 'bg-surface-container text-on-surface-variant' }}">
                Packing
            </a>
            <a href="{{ moduleRoute('getTable', ['tab' => 'delivery']) }}" wire:navigate
               class="px-4 py-2 rounded-lg text-sm font-semibold {{ $tab === 'delivery' ? 'bg-primary text-white' : 'bg-surface-container text-on-surface-variant' }}">
                Delivery
            </a>
            <a href="{{ moduleRoute('getTable', ['tab' => 'riwayat']) }}" wire:navigate
               class="px-4 py-2 rounded-lg text-sm font-semibold {{ $tab === 'riwayat' ? 'bg-primary text-white' : 'bg-surface-container text-on-surface-variant' }}">
                Riwayat Cetak
            </a>
        </div>

        {{-- Filters --}}
        <x-filter :per-page="25" :fields="$fields">
            <x-slot:advanced>
                @if ($tab === 'riwayat')
                <x-filter-item label="RFID" name="bersih_rfid" operator="$contains" placeholder="Sebagian RFID..." />
                <x-filter-item label="Rumah Sakit" name="bersih_id_rs" :options="$rsOptions" />
                <x-filter-item label="Status" name="bersih_status" :options="$statusOptions" />
                <x-filter-item label="Linen" name="linen_nama" operator="$contains" placeholder="Nama linen..." />
                @else
                <x-filter-item label="RFID" name="outstanding_rfid" operator="$contains" placeholder="Sebagian RFID..." />
                <x-filter-item label="RS Scan" name="outstanding_rs_scan" :options="$rsOptions" />
                <x-filter-item label="Ruangan" name="outstanding_id_ruangan" :options="$ruanganOptions" />
                <x-filter-item label="Asal (Kotor/Retur/Rewash)" name="outstanding_status_transaksi" :options="$statusOptions" />
                @endif
            </x-slot:advanced>
        </x-filter>

        {{-- Table --}}
        @if ($tab === 'packing')
        <div>
            <x-table>
                <x-slot:head>
                    <th>No.</th>
                    <x-table-sort field="outstanding_rfid" label="NO. RFID" :sortField="$sortField" :sortDir="$sortDir" />
                    <th>Linen</th>
                    <th>RS Scan</th>
                    <th>Ruangan</th>
                    <x-table-sort field="outstanding_status_transaksi" label="Asal" :sortField="$sortField" :sortDir="$sortDir" />
                    <th>Proses</th>
                    <x-table-sort field="outstanding_updated_at" label="Update" :sortField="$sortField" :sortDir="$sortDir" />
                </x-slot:head>

                <x-slot:body>
                    @foreach($data as $i => $table)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td class="whitespace-nowrap font-mono text-xs">{{ $table->outstanding_rfid }}</td>
                        <td>{{ $table->linen_nama ?? '-' }}</td>
                        <td>{{ $table->rs_nama ?? '-' }}</td>
                        <td>{{ $table->ruangan_nama ?? '-' }}</td>
                        <td>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $asalBadge($table->outstanding_status_transaksi) }}">
                                {{ $asalLabel($table->outstanding_status_transaksi) }}
                            </span>
                        </td>
                        <td>{{ $table->outstanding_status_proses }}</td>
                        <td class="whitespace-nowrap text-xs">{{ $table->outstanding_updated_at }}</td>
                    </tr>
                    @endforeach
                </x-slot:body>

                <x-slot:mobile>
                    <x-table-mobile-select :model="null" :total="$data"/>
                    <div class="p-3 space-y-3" id="mBody">
                        @foreach($data as $table)
                        <div class="border border-outline-variant rounded-xl p-4 bg-surface-container-lowest shadow-sm active:scale-[0.99] transition-transform" data-id="{{ $table->outstanding_rfid }}" onclick="mToggle(this)">
                            <div class="flex items-center gap-2 mb-2">
                                <span data-check class="icon-[tabler--circle] size-5 text-base-content/20 shrink-0"></span>
                                <p class="flex-1 min-w-0 text-sm font-bold font-mono text-on-surface truncate">{{ $table->outstanding_rfid }}</p>
                                <span class="inline-flex items-center shrink-0 px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $asalBadge($table->outstanding_status_transaksi) }}">
                                    {{ $asalLabel($table->outstanding_status_transaksi) }}
                                </span>
                            </div>
                            <div class="mb-3 rounded-lg bg-surface-container px-3 py-2 text-xs">
                                <div class="flex items-center justify-between gap-2">
                                    <span class="text-on-surface-variant shrink-0">Rumah Sakit</span>
                                    <span class="font-semibold text-primary text-right truncate">{{ $table->rs_nama ?? '-' }}</span>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-x-3 gap-y-2 mb-3 text-xs">
                                <div class="min-w-0">
                                    <p class="text-[10px] text-on-surface-variant uppercase tracking-wide mb-0.5">Linen</p>
                                    <p class="font-medium text-on-surface truncate">{{ $table->linen_nama ?? '-' }}</p>
                                </div>
                                <div class="min-w-0 text-right">
                                    <p class="text-[10px] text-on-surface-variant uppercase tracking-wide mb-0.5">Ruangan</p>
                                    <p class="font-medium text-on-surface truncate">{{ $table->ruangan_nama ?? '-' }}</p>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-[10px] text-on-surface-variant uppercase tracking-wide mb-0.5">Proses</p>
                                    <p class="font-medium text-on-surface">{{ $table->outstanding_status_proses }}</p>
                                </div>
                                <div class="min-w-0 text-right">
                                    <p class="text-[10px] text-on-surface-variant uppercase tracking-wide mb-0.5">Update</p>
                                    <p class="font-medium text-on-surface truncate">{{ $table->outstanding_updated_at }}</p>
                                </div>
                            </div>
                            <div class="flex items-center justify-between pt-2 border-t border-outline-variant/50">
                                <span class="text-[9px] font-mono text-on-surface-variant bg-surface-container px-2 py-0.5 rounded">{{ $table->outstanding_key }}</span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </x-slot:mobile>
            </x-table>
        </div>
        @endif

        @if ($tab === 'delivery')
        <div>
            <x-table>
                <x-slot:head>
                    <th>No.</th>
                    <x-table-sort field="outstanding_rfid" label="NO. RFID" :sortField="$sortField" :sortDir="$sortDir" />
                    <th>Linen</th>
                    <th>RS Scan</th>
                    <th>Ruangan</th>
                    <x-table-sort field="outstanding_status_transaksi" label="Asal" :sortField="$sortField" :sortDir="$sortDir" />
                    <th>Key Packing</th>
                    <x-table-sort field="outstanding_updated_at" label="Update" :sortField="$sortField" :sortDir="$sortDir" />
                </x-slot:head>

                <x-slot:body>
                    @foreach($data as $i => $table)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td class="whitespace-nowrap font-mono text-xs">{{ $table->outstanding_rfid }}</td>
                        <td>{{ $table->linen_nama ?? '-' }}</td>
                        <td>{{ $table->rs_nama ?? '-' }}</td>
                        <td>{{ $table->ruangan_nama ?? '-' }}</td>
                        <td>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $asalBadge($table->outstanding_status_transaksi) }}">
                                {{ $asalLabel($table->outstanding_status_transaksi) }}
                            </span>
                        </td>
                        <td class="whitespace-nowrap font-mono text-xs">{{ $table->outstanding_key }}</td>
                        <td class="whitespace-nowrap text-xs">{{ $table->outstanding_updated_at }}</td>
                    </tr>
                    @endforeach
                </x-slot:body>

                <x-slot:mobile>
                    <x-table-mobile-select :model="null" :total="$data"/>
                    <div class="p-3 space-y-3" id="mBody">
                        @foreach($data as $table)
                        <div class="border border-outline-variant rounded-xl p-4 bg-surface-container-lowest shadow-sm active:scale-[0.99] transition-transform" data-id="{{ $table->outstanding_rfid }}" onclick="mToggle(this)">
                            <div class="flex items-center gap-2 mb-2">
                                <span data-check class="icon-[tabler--circle] size-5 text-base-content/20 shrink-0"></span>
                                <p class="flex-1 min-w-0 text-sm font-bold font-mono text-on-surface truncate">{{ $table->outstanding_rfid }}</p>
                                <span class="inline-flex items-center shrink-0 px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $asalBadge($table->outstanding_status_transaksi) }}">
                                    {{ $asalLabel($table->outstanding_status_transaksi) }}
                                </span>
                            </div>
                            <div class="mb-3 rounded-lg bg-surface-container px-3 py-2 text-xs">
                                <div class="flex items-center justify-between gap-2">
                                    <span class="text-on-surface-variant shrink-0">Rumah Sakit</span>
                                    <span class="font-semibold text-primary text-right truncate">{{ $table->rs_nama ?? '-' }}</span>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-x-3 gap-y-2 mb-3 text-xs">
                                <div class="min-w-0">
                                    <p class="text-[10px] text-on-surface-variant uppercase tracking-wide mb-0.5">Linen</p>
                                    <p class="font-medium text-on-surface truncate">{{ $table->linen_nama ?? '-' }}</p>
                                </div>
                                <div class="min-w-0 text-right">
                                    <p class="text-[10px] text-on-surface-variant uppercase tracking-wide mb-0.5">Ruangan</p>
                                    <p class="font-medium text-on-surface truncate">{{ $table->ruangan_nama ?? '-' }}</p>
                                </div>
                            </div>
                            <div class="flex items-center justify-between pt-2 border-t border-outline-variant/50">
                                <span class="text-[9px] font-mono text-on-surface-variant bg-surface-container px-2 py-0.5 rounded">{{ $table->outstanding_key }}</span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </x-slot:mobile>
            </x-table>
        </div>
        @endif

        @if ($tab === 'riwayat')
        <x-table>
            <x-slot:head>
                <th>No.</th>
                <x-table-sort field="bersih_rfid" label="RFID" :sortField="$sortField" :sortDir="$sortDir" />
                <th>Linen</th>
                <th>RS</th>
                <th>Ruangan</th>
                <th>Status</th>
                <th>Barcode</th>
                <th>Delivery</th>
                <x-table-sort field="bersih_report" label="Report" :sortField="$sortField" :sortDir="$sortDir" />
                <th>User</th>
            </x-slot:head>

            <x-slot:body>
                @foreach($data as $i => $table)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td class="whitespace-nowrap font-mono text-xs">{{ $table->bersih_rfid }}</td>
                    <td>{{ $table->linen_nama ?? '-' }}</td>
                    <td>{{ $table->rs_nama ?? '-' }}</td>
                    <td>{{ $table->ruangan_nama ?? '-' }}</td>
                    <td><span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">{{ $table->bersih_status }}</span></td>
                    <td class="whitespace-nowrap font-mono text-xs">{{ $table->bersih_barcode ?? '-' }}</td>
                    <td class="whitespace-nowrap font-mono text-xs">{{ $table->bersih_delivery ?? '-' }}</td>
                    <td class="whitespace-nowrap text-xs">{{ $table->bersih_report }}</td>
                    <td>{{ $table->user_nama ?? '-' }}</td>
                </tr>
                @endforeach
            </x-slot:body>

            <x-slot:mobile>
                <x-table-mobile-select :model="null" :total="$data"/>
                <div class="p-3 space-y-3" id="mBody">
                    @foreach($data as $table)
                    <div class="border border-outline-variant rounded-xl p-4 bg-surface-container-lowest shadow-sm active:scale-[0.99] transition-transform" data-id="{{ $table->bersih_rfid }}" onclick="mToggle(this)">
                        <div class="flex items-center gap-2 mb-2">
                            <span data-check class="icon-[tabler--circle] size-5 text-base-content/20 shrink-0"></span>
                            <p class="flex-1 min-w-0 text-sm font-bold font-mono text-on-surface truncate">{{ $table->bersih_rfid }}</p>
                            <span class="inline-flex items-center shrink-0 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-green-100 text-green-800">{{ $table->bersih_status }}</span>
                        </div>
                        <div class="mb-3 rounded-lg bg-surface-container px-3 py-2 text-xs">
                            <div class="flex items-center justify-between gap-2">
                                <span class="text-on-surface-variant shrink-0">Rumah Sakit</span>
                                <span class="font-semibold text-primary text-right truncate">{{ $table->rs_nama ?? '-' }}</span>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-x-3 gap-y-2 mb-3 text-xs">
                            <div class="min-w-0">
                                <p class="text-[10px] text-on-surface-variant uppercase tracking-wide mb-0.5">Linen</p>
                                <p class="font-medium text-on-surface truncate">{{ $table->linen_nama ?? '-' }}</p>
                            </div>
                            <div class="min-w-0 text-right">
                                <p class="text-[10px] text-on-surface-variant uppercase tracking-wide mb-0.5">Ruangan</p>
                                <p class="font-medium text-on-surface truncate">{{ $table->ruangan_nama ?? '-' }}</p>
                            </div>
                            <div class="min-w-0">
                                <p class="text-[10px] text-on-surface-variant uppercase tracking-wide mb-0.5">Barcode</p>
                                <p class="font-mono font-medium text-on-surface truncate">{{ $table->bersih_barcode ?? '-' }}</p>
                            </div>
                            <div class="min-w-0 text-right">
                                <p class="text-[10px] text-on-surface-variant uppercase tracking-wide mb-0.5">Delivery</p>
                                <p class="font-mono font-medium text-on-surface truncate">{{ $table->bersih_delivery ?? '-' }}</p>
                            </div>
                            <div class="min-w-0">
                                <p class="text-[10px] text-on-surface-variant uppercase tracking-wide mb-0.5">Report</p>
                                <p class="font-medium text-on-surface truncate">{{ $table->bersih_report }}</p>
                            </div>
                            <div class="min-w-0 text-right">
                                <p class="text-[10px] text-on-surface-variant uppercase tracking-wide mb-0.5">User</p>
                                <p class="font-medium text-on-surface truncate">{{ $table->user_nama ?? '-' }}</p>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </x-slot:mobile>
        </x-table>
        @endif

        <x-pagination :paginator="$data" />

    </div>

    <input type="hidden" class="module" value="{{ Str::beforeLast(request()->route()->uri(), '/') }}">
    <script src="/js/table.js"></script>
    <script>initTable('{{ $sortField }}', '{{ $sortDir }}');</script>
    <script>
        // ponytail: table.js buildUrl() tidak mengenal ?tab= — pertahankan tab aktif
        // saat filter/sort/pagination agar tidak mental ke tab packing.
        (function () {
            var currentTab = @json($tab);
            var _buildUrl = window.buildUrl;
            window.buildUrl = function () {
                var q = ((document.getElementById('searchInput') || {}).value || '').trim();
                var fieldEl = document.getElementById('filterField');
                var field = fieldEl ? fieldEl.value : '';
                var perPage = (document.getElementById('perPage') || {}).value || '25';
                var params = new URLSearchParams();
                params.set('tab', currentTab);
                if (q) {
                    params.set('filters[' + field + '][$contains]', q);
                    params.set('_field', field);
                    params.set('_q', q);
                }
                document.querySelectorAll('[data-field]').forEach(function (input) {
                    var fieldName = input.dataset.field;
                    var opEl = document.querySelector('[data-op="' + fieldName + '"]');
                    var operator = opEl ? opEl.value : '$eq';
                    var value = input.tagName === 'SELECT' ? input.value : input.value.trim();
                    if (value) {
                        params.set('filters[' + fieldName + '][' + operator + ']', value);
                        params.set('filter_op[' + fieldName + ']', operator);
                    }
                });
                if (window.currentSortField) params.set('sort[0]', window.currentSortField + ':' + window.currentSortDir);
                params.set('per_page', perPage);
                window.location.href = window.getModulePath() + '/table?' + params.toString();
            };
        })();
    </script>
    <style>
        .top-x-scroll { height: 14px; }
        .top-x-scroll > div { height: 1px; }
    </style>
    <script>
        (function () {
            function wireTopScroll() {
                var wrap = document.querySelector('.form-card .overflow-x-auto:not(.top-x-scroll)');
                if (!wrap || wrap.previousElementSibling?.classList?.contains('top-x-scroll')) return;
                var top = document.createElement('div');
                top.className = 'overflow-x-auto top-x-scroll hidden lg:block';
                var inner = document.createElement('div');
                top.appendChild(inner);
                wrap.parentNode.insertBefore(top, wrap);
                var syncWidth = function () { inner.style.width = wrap.scrollWidth + 'px'; };
                syncWidth();
                var lock = false;
                top.addEventListener('scroll', function () {
                    if (lock) return;
                    lock = true;
                    wrap.scrollLeft = top.scrollLeft;
                    lock = false;
                });
                wrap.addEventListener('scroll', function () {
                    if (lock) return;
                    lock = true;
                    top.scrollLeft = wrap.scrollLeft;
                    lock = false;
                });
                window.addEventListener('resize', syncWidth);
            }
            wireTopScroll();
            document.addEventListener('livewire:navigated', wireTopScroll);
        })();
    </script>
</x-layouts::app>
