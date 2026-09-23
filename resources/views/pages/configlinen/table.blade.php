<?php /** @var App\Models\ConfigLinen $table */ ?>

<x-layouts::app>
    <x-breadcrumb :items="[['url' => '/dashboard', 'label' => 'Home'], ['url' => '', 'label' => moduleLabel()]]" />
    <div class="content mt-4 lg:mt-0">
        {{-- Filters --}}
        <x-filter :per-page="25" :fields="$fields">
            <x-slot:advanced>
                @foreach ($fields as $key => $advance)
                <x-filter-item :label="$advance" :name="$key"/>
                @endforeach
            </x-slot:advanced>
        </x-filter>

        {{-- Table --}}
        @php
            $currentSort = request('sort.0', '');
            $sortField = str_replace(':desc','',str_replace(':asc','',$currentSort));
            $sortDir = str_contains($currentSort, ':desc') ? 'desc' : 'asc';
        @endphp

        <x-table>
            <x-slot:head>
                <x-table-checkbox :model="$model" onchange="toggleAll(this)" />
                <th>Actions</th>
                <x-table-sort field="detail_rfid" label="RFID" :sortField="$sortField" :sortDir="$sortDir" />
                <x-table-sort field="rs_nama" label="RS Pemilik (Master)" :sortField="$sortField" :sortDir="$sortDir" />
                <th>Dipakai Sekarang</th>
                <x-table-sort field="kepemilikan" label="Kepemilikan" :sortField="$sortField" :sortDir="$sortDir" />
            </x-slot:head>

            <x-slot:body>
                @foreach($data as $table)
                @php $pairId = $table->detail_rfid.':'.$table->rs_id; @endphp
                <tr>
                    <x-table-row-checkbox :model="$model" :value="$pairId" />
                    <x-table-action :model="$model" :id="$pairId" />
                    <td class="whitespace-nowrap font-mono text-xs">{{ $table->detail_rfid }}</td>
                    <td>{{ $table->rs_nama ?? ('RS #'.$table->rs_id) }}</td>
                    <td>{{ $table->current_nama ?? '-' }}</td>
                    <td>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ ($table->kepemilikan ?? '') === 'FREE' ? 'bg-green-100 text-green-800' : (($table->kepemilikan ?? '') === 'GROUP' ? 'bg-blue-100 text-blue-800' : 'bg-amber-100 text-amber-800') }}">
                            {{ $table->kepemilikan ?? '-' }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </x-slot:body>

            <x-slot:mobile>
                <x-table-mobile-select :model="$model" :total="$data"/>
                <div class="p-3 space-y-3" id="mBody">
                    @foreach($data as $table)
                    @php $pairId = $table->detail_rfid.':'.$table->rs_id; @endphp
                    <div class="border border-outline-variant rounded-xl p-4 bg-surface-container-lowest shadow-sm" data-id="{{ $pairId }}">
                        <p class="text-sm font-bold text-on-surface truncate font-mono mb-3">{{ $table->detail_rfid }}</p>
                        <div class="grid grid-cols-2 gap-3 mb-3">
                            <div>
                                <p class="text-[10px] text-on-surface-variant uppercase tracking-wide mb-0.5">RS Pemilik (Master)</p>
                                <p class="text-xs font-medium text-primary truncate">{{ $table->rs_nama ?? ('RS #'.$table->rs_id) }}</p>
                            </div>
                            <div>
                                <p class="text-[10px] text-on-surface-variant uppercase tracking-wide mb-0.5">Dipakai Sekarang</p>
                                <p class="text-xs font-medium text-on-surface truncate">{{ $table->current_nama ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-[10px] text-on-surface-variant uppercase tracking-wide mb-0.5">Kepemilikan</p>
                                <p class="text-xs font-medium text-on-surface truncate">{{ $table->kepemilikan ?? '-' }}</p>
                            </div>
                        </div>
                        <div class="flex items-center justify-between pt-2 border-t border-outline-variant/50">
                            <span class="text-[9px] font-mono text-on-surface-variant bg-surface-container px-2 py-0.5 rounded">{{ $pairId }}</span>
                            <div class="flex gap-1" onclick="event.stopPropagation()">
                                <x-table-action :model="$model" :id="$pairId" />
                            </div>
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
</x-layouts::app>

