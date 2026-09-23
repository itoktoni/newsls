<?php /** @var App\Models\GroupRs $table */ ?>

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
                <x-table-sort field="group_rs_code" label="Kode" :sortField="$sortField" :sortDir="$sortDir" />
                <x-table-sort field="group_rs_nama" label="Nama" :sortField="$sortField" :sortDir="$sortDir" />
            </x-slot:head>

            <x-slot:body>
                @foreach($data as $table)
                <tr>
                    <x-table-row-checkbox :model="$model" :value="$table->field_primary" />
                    <x-table-action :model="$model" :id="$table->field_primary" />
                    <td>{{ $table->group_rs_code ?? '-' }}</td>
                    <td>{{ $table->group_rs_nama }}</td>
                </tr>
                @endforeach
            </x-slot:body>

            <x-slot:mobile>
                <x-table-mobile-select :model="$model" :total="$data"/>
                <div class="p-3 space-y-3" id="mBody">
                    @foreach($data as $table)
                    <div class="border border-outline-variant rounded-xl p-4 bg-surface-container-lowest shadow-sm active:scale-[0.99] transition-transform" data-id="{{ $table->field_primary }}" onclick="mToggle(this)">
                        {{-- header --}}
                        <div class="flex items-start gap-2 mb-3">
                            <span data-check class="icon-[tabler--circle] size-5 text-base-content/20 shrink-0 mt-0.5"></span>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-bold text-on-surface truncate">{{ $table->group_rs_nama }}</p>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-x-3 gap-y-2 mb-3 text-xs">
                            <div class="min-w-0 ">
                                <p class="text-[10px] text-on-surface-variant uppercase tracking-wide mb-0.5">Kode</p>
                                <p class="font-medium text-on-surface truncate">{{ $table->group_rs_code ?? '-' }}</p>
                            </div>
                            <div class="min-w-0 text-right">
                                <p class="text-[10px] text-on-surface-variant uppercase tracking-wide mb-0.5">Nama</p>
                                <p class="font-medium text-on-surface truncate">{{ $table->group_rs_nama }}</p>
                            </div>

                        </div>
                        <div class="flex items-center justify-between pt-2 border-t border-outline-variant/50">
                            <span class="text-[9px] font-mono text-on-surface-variant bg-surface-container px-2 py-0.5 rounded">{{ $table->field_primary }}</span>
                            <div class="flex gap-1" onclick="event.stopPropagation()">
                                <x-table-action :model="$model" :id="$table->field_primary" />
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

