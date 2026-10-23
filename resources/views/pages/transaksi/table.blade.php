<?php /** @var App\Models\TransaksiMaster $table */ ?>

<x-layouts::app>
    <x-breadcrumb :items="[['url' => '/dashboard', 'label' => 'Home'], ['url' => '', 'label' => moduleLabel()]]" />
    <div class="content mt-4 lg:mt-0">
        <x-filter :per-page="25" :fields="$fields">
            <x-slot:advanced>
                @foreach ($fields as $key => $advance)
                <x-filter-item :label="$advance" :name="$key"/>
                @endforeach

                <x-button variant="primary" class="btn-block" onclick="applyAdvanced()">Apply</x-button>
                <x-button variant="soft" class="btn-block" onclick="resetAdvanced()">Reset</x-button>
            </x-slot:advanced>
        </x-filter>

        @php
            $currentSort = request('sort.0', '');
            $sortField = str_replace(':desc','',str_replace(':asc','',$currentSort));
            $sortDir = str_contains($currentSort, ':desc') ? 'desc' : 'asc';
        @endphp

        <x-table>
            <x-slot:head>
                <x-table-checkbox :model="$model" onchange="toggleAll(this)" />
                <th>Actions</th>
                <th>No.</th>
                <x-table-sort field="master_kode" label="KODE TRANSAKSI" :sortField="$sortField" :sortDir="$sortDir" />
                <x-table-sort field="master_tanggal" label="TANGGAL" :sortField="$sortField" :sortDir="$sortDir" />
                <th>RUMAH SAKIT</th>
                <th>OPERATOR</th>
                <x-table-sort field="master_status" label="STATUS" :sortField="$sortField" :sortDir="$sortDir" />
                <th class="num">KOTOR (KG)</th>
                <th class="num">BERSIH (KG)</th>
                <th class="num">SELISIH (KG)</th>
                <th>DETAIL</th>
            </x-slot:head>

            <x-slot:body>
                @foreach($data as $i => $table)
                <tr>
                    <x-table-row-checkbox :model="$model" :value="$table->field_primary" />
                    <x-table-action :model="$model" :id="$table->field_primary">
                        @can('show', $model)
                        <a href="{{ moduleRoute('getShow', ['id' => $table->field_primary]) }}" wire:navigate title="Detail" class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-violet-500/10 text-violet-600 hover:bg-violet-500/20 transition-colors">
                            <span class="material-symbols-outlined text-lg">list_alt</span>
                        </a>
                        @endcan
                    </x-table-action>
                    <td>{{ $i + 1 }}</td>
                    <td class="font-mono text-xs">{{ $table->master_kode }}</td>
                    <td class="whitespace-nowrap text-xs">{{ formatDate($table->master_tanggal) ?? '-' }}</td>
                    <td>{{ $table->rs_nama ?? '-' }}</td>
                    <td>{{ $table->operator_name ?? '-' }}</td>
                    <td>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $table->master_status === 'KOTOR' ? 'bg-amber-100 text-amber-800' : ($table->master_status === 'REWASH' ? 'bg-blue-100 text-blue-800' : 'bg-red-100 text-red-800') }}">
                            {{ $table->master_status }}
                        </span>
                    </td>
                    <td class="text-right">{{ $table->master_qty !== null ? formatQty($table->master_qty) : '-' }}</td>
                    <td class="text-right">{{ formatQty($table->bersih_kg ?? 0) }}</td>
                    <td class="text-right">{{ $table->master_qty !== null ? formatQty($table->master_qty - ($table->bersih_kg ?? 0)) : '-' }}</td>
                    <td class="text-right">{{ $table->detail_count ?? '-' }}</td>
                </tr>
                @endforeach
            </x-slot:body>

            <x-slot:mobile>
                <x-table-mobile-select :model="$model" :total="$data"/>
                <div class="p-3 space-y-3" id="mBody">
                    @foreach($data as $table)
                    <div class="border border-outline-variant rounded-xl p-4 bg-surface-container-lowest shadow-sm active:scale-[0.99] transition-transform" data-id="{{ $table->field_primary }}" onclick="mToggle(this)">
                        <div class="flex items-center gap-2 mb-2">
                            <span data-check class="icon-[tabler--circle] size-5 text-base-content/20 shrink-0"></span>
                            <p class="flex-1 min-w-0 text-sm font-bold font-mono text-on-surface truncate">{{ $table->master_kode }}</p>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $table->master_status === 'KOTOR' ? 'bg-amber-100 text-amber-800' : ($table->master_status === 'REWASH' ? 'bg-blue-100 text-blue-800' : 'bg-red-100 text-red-800') }}">{{ $table->master_status }}</span>
                        </div>
                        <div class="grid grid-cols-2 gap-x-3 gap-y-2 mb-3 text-xs">
                            <div>
                                <p class="text-[10px] text-on-surface-variant uppercase tracking-wide mb-0.5">TANGGAL</p>
                                <p class="font-medium text-on-surface truncate">{{ formatDate($table->master_tanggal) ?? '-' }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-[10px] text-on-surface-variant uppercase tracking-wide mb-0.5">KOTOR / BERSIH (KG)</p>
                                <p class="font-medium text-on-surface truncate">{{ $table->master_qty !== null ? formatQty($table->master_qty) : '-' }} / {{ formatQty($table->bersih_kg ?? 0) }}</p>
                            </div>
                            <div>
                                <p class="text-[10px] text-on-surface-variant uppercase tracking-wide mb-0.5">RUMAH SAKIT</p>
                                <p class="font-medium text-on-surface truncate">{{ $table->rs_nama ?? '-' }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-[10px] text-on-surface-variant uppercase tracking-wide mb-0.5">OPERATOR</p>
                                <p class="font-medium text-on-surface truncate">{{ $table->operator_name ?? '-' }}</p>
                            </div>
                        </div>
                        <div class="flex items-center justify-between pt-2 border-t border-outline-variant/50">
                            <span class="text-[9px] font-mono text-on-surface-variant bg-surface-container px-2 py-0.5 rounded">{{ $table->field_primary }}</span>
                            @can('show', $model)
                            <a href="{{ moduleRoute('getShow', ['id' => $table->field_primary]) }}" wire:navigate class="text-xs font-semibold text-violet-600">Lihat detail →</a>
                            @endcan
                        </div>
                    </div>
                    @endforeach
                </div>
            </x-slot:mobile>
        </x-table>

        <x-pagination :paginator="$data" />
        <x-action :model="$model" :action="['create', 'delete']"/>
    </div>

    <input type="hidden" class="module" value="{{ Str::beforeLast(request()->route()->uri(), '/') }}">
    <script src="/js/table.js"></script>
    <script>initTable('{{ $sortField }}', '{{ $sortDir }}');</script>
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
