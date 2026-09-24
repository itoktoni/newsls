<?php /** @var App\Models\Activity $table */ ?>

<x-layouts::app>
    <x-breadcrumb :items="[['url' => '/dashboard', 'label' => 'Home'], ['url' => '', 'label' => moduleLabel()]]" />
    <div class="content mt-4 lg:mt-0">
        {{-- Filters --}}
        <x-filter :per-page="25" :fields="$fields" searchPlaceholder="Cari RFID / nama operasi / user...">
            <x-slot:advanced>
                <x-filter-item label="ID (RFID)" name="subject_id" operator="$contains" placeholder="Sebagian RFID..." />
                <x-filter-item label="Event" name="event" :options="$eventOptions ?? []" />
                <x-filter-item label="Nama" name="log_name" :options="$logNameOptions ?? []" />
                <x-filter-item label="Model" name="subject_type" :options="$subjectTypeOptions ?? []" />
                <x-filter-item label="User" name="user_name" operator="$contains" placeholder="Sebagian nama user..." />
                <x-filter-item label="Deskripsi" name="description" operator="$contains" placeholder="Sebagian deskripsi..." />
                <x-filter-item label="Tanggal" name="created_at" type="date" />
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
                <x-table-sort field="log_name" label="Nama" :sortField="$sortField" :sortDir="$sortDir" />
                <x-table-sort field="subject_id" label="ID (RFID)" :sortField="$sortField" :sortDir="$sortDir" />
                <x-table-sort field="subject_model_name" label="Model" :sortField="$sortField" :sortDir="$sortDir" />
                <x-table-sort field="causer_name" label="User" :sortField="$sortField" :sortDir="$sortDir" />
                <x-table-sort field="created_at" label="Tanggal" :sortField="$sortField" :sortDir="$sortDir" />
            </x-slot:head>

            <x-slot:body>
                @foreach($data as $table)
                <tr>
                    <x-table-row-checkbox :model="$model" :value="$table->field_primary" />
                    <x-table-action :model="$model" :id="$table->field_primary" />
                    <td>{{ $table->log_name ?? '-' }}</td>
                    <td class="font-mono text-xs">{{ $table->subject_id ?? '-' }}</td>
                    <td class="truncate max-w-[12rem]" title="{{ $table->subject_type }}">{{ $table->subject_model_name }}</td>
                    <td>{{ $table->causer_name ?? '-' }}</td>
                    <td class="whitespace-nowrap text-xs">{{ formatDate($table->created_at, true) ?? '-' }}</td>
                </tr>
                @endforeach
            </x-slot:body>

            <x-slot:mobile>
                <x-table-mobile-select :model="$model" :total="$data"/>
                <div class="p-3 space-y-3" id="mBody">
                    @foreach($data as $table)
                    @php
                        $eventColor = match ($table->event) {
                            'created' => 'bg-green-100 text-green-800',
                            'updated' => 'bg-amber-100 text-amber-800',
                            'deleted' => 'bg-red-100 text-red-800',
                            default => 'bg-gray-100 text-gray-700',
                        };
                    @endphp
                    <div class="border border-outline-variant rounded-xl p-4 bg-surface-container-lowest shadow-sm active:scale-[0.99] transition-transform" data-id="{{ $table->field_primary }}" onclick="mToggle(this)">
                        <div class="flex items-center gap-2 mb-2">
                            <span data-check class="icon-[tabler--circle] size-5 text-base-content/20 shrink-0"></span>
                            <p class="flex-1 min-w-0 text-sm font-bold text-on-surface truncate">{{ $table->log_name ?? '-' }}</p>
                            <span class="inline-flex items-center shrink-0 px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $eventColor }}">{{ $table->event ?? '-' }}</span>
                        </div>
                        <p class="text-[11px] font-mono text-on-surface-variant truncate mb-3">RFID: {{ $table->subject_id ?? '-' }}</p>
                        <div class="grid grid-cols-2 gap-x-3 gap-y-2 mb-3 text-xs">
                            <div class="min-w-0">
                                <p class="text-[10px] text-on-surface-variant uppercase tracking-wide mb-0.5">Model</p>
                                <p class="font-medium text-on-surface truncate" title="{{ $table->subject_type }}">{{ $table->subject_model_name }}</p>
                            </div>
                            <div class="min-w-0 text-right">
                                <p class="text-[10px] text-on-surface-variant uppercase tracking-wide mb-0.5">User</p>
                                <p class="font-medium text-primary truncate">{{ $table->causer_name ?? '-' }}</p>
                            </div>
                            <div class="min-w-0">
                                <p class="text-[10px] text-on-surface-variant uppercase tracking-wide mb-0.5">Tanggal</p>
                                <p class="font-medium text-on-surface truncate">{{ formatDate($table->created_at, true) ?? '-' }}</p>
                            </div>
                            <div class="min-w-0 text-right">
                                <p class="text-[10px] text-on-surface-variant uppercase tracking-wide mb-0.5">No.</p>
                                <p class="font-mono font-medium text-on-surface-variant truncate">#{{ $table->field_primary }}</p>
                            </div>
                        </div>
                        <div class="flex items-center justify-between pt-2 border-t border-outline-variant/50">
                            <span class="text-[9px] font-mono text-on-surface-variant bg-surface-container px-2 py-0.5 rounded">#{{ $table->field_primary }}</span>
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

