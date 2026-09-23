<?php /** @var App\Models\Opname $table */ ?>

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
            $statusLabel = static fn ($status) => match ((int) $status) {
                1 => 'Proses',
                2 => 'Selesai',
                default => 'Draft',
            };
            $statusBadge = static fn ($status) => match ((int) $status) {
                1 => 'bg-amber-100 text-amber-800',
                2 => 'bg-green-100 text-green-800',
                default => 'bg-slate-100 text-slate-700',
            };
        @endphp

        <x-table>
            <x-slot:head>
                <x-table-checkbox :model="$model" onchange="toggleAll(this)" />
                <th>Actions</th>
                <x-table-sort field="opname_id" label="ID" :sortField="$sortField" :sortDir="$sortDir" />
                <x-table-sort field="opname_nama" label="Nama" :sortField="$sortField" :sortDir="$sortDir" />
                <th>RS</th>
                <x-table-sort field="opname_mulai" label="Mulai" :sortField="$sortField" :sortDir="$sortDir" />
                <x-table-sort field="opname_selesai" label="Selesai" :sortField="$sortField" :sortDir="$sortDir" />
                <x-table-sort field="opname_status" label="Status" :sortField="$sortField" :sortDir="$sortDir" />
                <th>Capture</th>
            </x-slot:head>

            <x-slot:body>
                @foreach($data as $table)
                <tr>
                    <x-table-row-checkbox :model="$model" :value="$table->field_primary" />
                    <x-table-action :model="$model" :id="$table->field_primary">
                        @can('update', $model)
                        @if (empty($table->opname_capture))
                        <a href="{{ route('opname.capture', ['code' => $table->field_primary]) }}" wire:navigate title="Capture" class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-emerald-500/10 text-emerald-600 hover:bg-emerald-500/20 transition-colors">
                            <span class="material-symbols-outlined text-lg">photo_camera</span>
                        </a>
                        @endif
                        <a href="{{ route('opname.sync', ['code' => $table->field_primary]) }}" wire:navigate title="Sync" class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-sky-500/10 text-sky-600 hover:bg-sky-500/20 transition-colors">
                            <span class="material-symbols-outlined text-lg">sync</span>
                        </a>
                        <a href="{{ route('opname.detail', ['code' => $table->field_primary]) }}" title="Detail" class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-violet-500/10 text-violet-600 hover:bg-violet-500/20 transition-colors">
                            <span class="material-symbols-outlined text-lg">list_alt</span>
                        </a>
                        @endcan
                    </x-table-action>
                    <td>{{ $table->opname_id }}</td>
                    <td class="max-w-[200px] truncate">{{ $table->opname_nama }}</td>
                    <td>{{ $table->rs_nama ?? $table->opname_id_rs }}</td>
                    <td>{{ $table->opname_mulai?->format('Y-m-d') ?? '-' }}</td>
                    <td>{{ $table->opname_selesai?->format('Y-m-d') ?? '-' }}</td>
                    <td>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusBadge($table->opname_status) }}">
                            {{ $statusLabel($table->opname_status) }}
                        </span>
                    </td>
                    <td>
                        @if ($table->opname_capture)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800" title="{{ $table->opname_capture }}">
                            {{ \Carbon\Carbon::parse($table->opname_capture)->format('Y-m-d H:i') }}
                        </span>
                        @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-600">Belum</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </x-slot:body>

            <x-slot:mobile>
                <x-table-mobile-select :model="$model" :total="$data"/>
                <div class="p-3 space-y-3" id="mBody">
                    @foreach($data as $table)
                    <div class="border border-outline-variant rounded-xl p-4 bg-surface-container-lowest shadow-sm active:scale-[0.99] transition-transform" data-id="{{ $table->field_primary }}" onclick="mToggle(this)">
                        <div class="flex items-start gap-2 mb-3">
                            <span data-check class="icon-[tabler--circle] size-5 text-base-content/20 shrink-0 mt-0.5"></span>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-bold text-on-surface truncate">{{ $table->opname_nama }}</p>
                                <p class="text-[11px] text-primary truncate">{{ $table->rs_nama ?? ('RS #'.$table->opname_id_rs) }}</p>
                            </div>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium {{ $statusBadge($table->opname_status) }}">
                                {{ $statusLabel($table->opname_status) }}
                            </span>
                        </div>
                        <div class="grid grid-cols-2 gap-x-3 gap-y-2 mb-3 text-xs">
                            <div class="min-w-0">
                                <p class="text-[10px] text-on-surface-variant uppercase tracking-wide mb-0.5">Mulai</p>
                                <p class="font-medium text-primary truncate">{{ $table->opname_mulai?->format('Y-m-d') ?? '-' }}</p>
                            </div>
                            <div class="min-w-0 text-right">
                                <p class="text-[10px] text-on-surface-variant uppercase tracking-wide mb-0.5">Selesai</p>
                                <p class="font-medium text-on-surface truncate">{{ $table->opname_selesai?->format('Y-m-d') ?? '-' }}</p>
                            </div>
                            <div class="min-w-0">
                                <p class="text-[10px] text-on-surface-variant uppercase tracking-wide mb-0.5">Capture</p>
                                <p class="font-medium text-on-surface truncate">{{ $table->opname_capture ? \Carbon\Carbon::parse($table->opname_capture)->format('Y-m-d H:i') : 'Belum' }}</p>
                            </div>
                            <div class="min-w-0 text-right">
                                <p class="text-[10px] text-on-surface-variant uppercase tracking-wide mb-0.5">ID</p>
                                <p class="font-medium text-on-surface truncate">{{ $table->opname_id }}</p>
                            </div>
                        </div>
                        <div class="flex items-center justify-between pt-2 border-t border-outline-variant/50">
                            <span class="text-[9px] font-mono text-on-surface-variant bg-surface-container px-2 py-0.5 rounded">{{ $table->field_primary }}</span>
                            <div class="flex gap-1" onclick="event.stopPropagation()">
                                <x-table-action :model="$model" :id="$table->field_primary">
                                    @can('update', $model)
                                    @if (empty($table->opname_capture))
                                    <a href="{{ route('opname.capture', ['code' => $table->field_primary]) }}" title="Capture" class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-emerald-500/10 text-emerald-600 hover:bg-emerald-500/20 transition-colors">
                                        <span class="material-symbols-outlined text-lg">photo_camera</span>
                                    </a>
                                    @endif
                                    <a href="{{ route('opname.sync', ['code' => $table->field_primary]) }}" title="Sync" class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-sky-500/10 text-sky-600 hover:bg-sky-500/20 transition-colors">
                                        <span class="material-symbols-outlined text-lg">sync</span>
                                    </a>
                                    @endcan
                                </x-table-action>
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

    <input type="hidden" class="module" value="{{ Str::beforeLast(request()->route()->uri(), '/') }}" />
    <script src="/js/table.js"></script>
    <script>initTable('{{ $sortField }}', '{{ $sortDir }}');</script>
</x-layouts::app>
