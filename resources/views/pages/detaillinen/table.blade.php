<?php /** @var App\Models\DetailLinen $table */ ?>

<x-layouts::app>
    <x-breadcrumb :items="[['url' => '/dashboard', 'label' => 'Home'], ['url' => '', 'label' => moduleLabel()]]" />
    <div class="content mt-4 lg:mt-0">
        {{-- Filters --}}
        <x-filter :per-page="25" :fields="$fields">
            <x-slot:advanced>
                <x-filter-item label="RFID" name="detail_rfid" operator="$contains" placeholder="Sebagian RFID..." />
                <x-filter-item label="Dipakai RS Sekarang" name="detail_id_rs" :options="$rs" />
                <x-filter-item label="Kepemilikan" name="detail_status_kepemilikan" :options="$milik" />
                <x-filter-item label="Ruangan" name="detail_id_ruangan" :options="$ruangan" />
                <x-filter-item label="Jenis Linen" name="detail_id_jenis" :options="$jenis" />
                <x-filter-item label="Bahan" name="detail_id_bahan" :options="$bahan" />
                <x-filter-item label="Supplier" name="detail_id_supplier" :options="$supplier" />
                <x-filter-item label="Status Linen" name="detail_status_linen" :options="$linen" />
                <x-filter-item label="Total Bersih (Kotor)" name="detail_total_bersih" type="number" operator="$gte" placeholder="Min. dicuci..." />
                <x-filter-item label="Total Reject (Retur)" name="detail_total_reject" type="number" operator="$gte" placeholder="Min. reject..." />
                <x-filter-item label="Total Rewash" name="detail_total_rewash" type="number" operator="$gte" placeholder="Min. rewash..." />
                <x-filter-item label="Tanggal Cek" name="detail_tgl_cek" type="date" />
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
                <x-table-sort field="rs_nama" label="Rumah Sakit" :sortField="$sortField" :sortDir="$sortDir" />
                <x-table-sort field="detail_status_kepemilikan" label="Kepemilikan" :sortField="$sortField" :sortDir="$sortDir" />
                <x-table-sort field="ruangan_nama" label="Ruangan" :sortField="$sortField" :sortDir="$sortDir" />
                <x-table-sort field="jenis_nama" label="Jenis Linen" :sortField="$sortField" :sortDir="$sortDir" />
                <x-table-sort field="detail_total_bersih" label="Pemakaian" :sortField="$sortField" :sortDir="$sortDir" />
                <x-table-sort field="detail_updated_at" label="Tgl Terakhir" :sortField="$sortField" :sortDir="$sortDir" />
            </x-slot:head>

            <x-slot:body>
                @foreach($data as $table)
                <tr>
                    <x-table-row-checkbox :model="$model" :value="$table->field_primary" />
                    <x-table-action :model="$model" :id="$table->field_primary" />
                    <td class="whitespace-nowrap font-mono text-xs">{{ $table->detail_rfid }}</td>
                    <td>{{ $table->rs_nama ?? '-' }}</td>
                    <td>
                        {{ $table->detail_status_kepemilikan ?? '-' }}
                    </td>
                    <td>{{ $table->ruangan_nama ?? '-' }}</td>
                    <td>{{ $table->jenis_nama ?? '-' }}</td>

                    <td class="text-center">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                            {{ $table->detail_total_bersih ?? 0 }}x
                        </span>
                    </td>
                    <td class="whitespace-nowrap text-on-surface-variant">{{ formatDate($table->detail_created_at) ?? '-' }}</td>
                </tr>
                @endforeach
            </x-slot:body>

            <x-slot:mobile>
                <x-table-mobile-select :model="$model" :total="$data"/>
                <div class="p-3 space-y-3" id="mBody">
                    @foreach($data as $table)
                    <div class="border border-outline-variant rounded-xl p-4 bg-surface-container-lowest shadow-sm active:scale-[0.99] transition-transform" data-id="{{ $table->field_primary }}" onclick="mToggle(this)">
                        {{-- header: check + RFID + status linen --}}
                        <div class="flex items-center gap-2 mb-2">
                            <span data-check class="icon-[tabler--circle] size-5 text-base-content/20 shrink-0"></span>
                            <p class="flex-1 min-w-0 text-sm font-bold font-mono text-on-surface truncate">{{ $table->detail_rfid }}</p>
                            <span class="inline-flex items-center shrink-0 px-2 py-0.5 rounded-full text-[10px] font-semibold {{ in_array($table->detail_status_linen, ['BERSIH', 'GUDANG']) ? 'bg-green-100 text-green-800' : ($table->detail_status_linen === 'KOTOR' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-700') }}">
                                {{ $table->detail_status_linen ?? '-' }}
                            </span>
                        </div>
                        {{-- rumah sakit --}}
                        <div class="mb-3 rounded-lg bg-surface-container px-3 py-2 text-xs">
                            <div class="flex items-center justify-between gap-2">
                                <span class="text-on-surface-variant shrink-0">Rumah Sakit</span>
                                <span class="font-semibold text-primary text-right truncate">{{ $table->rs_nama ?? '-' }}</span>
                            </div>
                        </div>
                        {{-- detail grid --}}
                        <div class="grid grid-cols-2 gap-x-3 gap-y-2 mb-3 text-xs">
                            <div class="min-w-0">
                                <p class="text-[10px] text-on-surface-variant uppercase tracking-wide mb-0.5">Ruangan</p>
                                <p class="font-medium text-on-surface truncate">{{ $table->ruangan_nama ?? '-' }}</p>
                            </div>
                            <div class="min-w-0 text-right">
                                <p class="text-[10px] text-on-surface-variant uppercase tracking-wide mb-0.5">Jenis Linen</p>
                                <p class="font-medium text-on-surface truncate">{{ $table->jenis_nama ?? '-' }}</p>
                            </div>
                            <div class="min-w-0">
                                <p class="text-[10px] text-on-surface-variant uppercase tracking-wide mb-0.5">Cuci</p>
                                <p class="font-medium text-on-surface">{{ $table->detail_status_cuci ?? '-' }}</p>
                            </div>
                            <div class="min-w-0 text-right">
                                <p class="text-[10px] text-on-surface-variant uppercase tracking-wide mb-0.5">Dicek</p>
                                <p class="font-medium text-on-surface truncate">{{ formatDate($table->detail_tgl_cek) ?? '-' }}</p>
                            </div>
                            <div class="min-w-0">
                                <p class="text-[10px] text-on-surface-variant uppercase tracking-wide mb-0.5">Total Bersih</p>
                                <p class="font-bold text-green-700">{{ $table->detail_total_bersih ?? 0 }}x</p>
                            </div>
                            <div class="min-w-0 text-right">
                                <p class="text-[10px] text-on-surface-variant uppercase tracking-wide mb-0.5">Reject / Rewash</p>
                                <p class="font-medium text-on-surface"><span class="text-red-700 font-bold">{{ $table->detail_total_reject ?? 0 }}x</span> / <span class="text-blue-700 font-bold">{{ $table->detail_total_rewash ?? 0 }}x</span></p>
                            </div>
                        </div>
                        <div class="flex items-center justify-between pt-2 border-t border-outline-variant/50">
                            <div class="flex items-center gap-1.5">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $table->detail_status_kepemilikan === 'FREE' ? 'bg-green-100 text-green-800' : ($table->detail_status_kepemilikan === 'GROUP' ? 'bg-blue-100 text-blue-800' : 'bg-amber-100 text-amber-800') }}">
                                    {{ $table->detail_status_kepemilikan ?? '-' }}
                                </span>
                                <span class="text-[10px] text-on-surface-variant">{{ $table->detail_status_register ?? '' }}</span>
                            </div>
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
    <script>
        // ponytail: dropdown Ruangan/Jenis di advance filter dependen terhadap
        // RS terpilih (pivot rs_dan_ruangan / rs_dan_jenis). Tanpa RS = semua.
        (function () {
            var ruanganByRs = @json($ruanganByRs ?? []);
            var jenisByRs = @json($jenisByRs ?? []);
            var rsSel = document.querySelector('#advFilter select[data-field="detail_id_rs"]');
            var ruanganSel = document.querySelector('#advFilter select[data-field="detail_id_ruangan"]');
            var jenisSel = document.querySelector('#advFilter select[data-field="detail_id_jenis"]');
            if (!rsSel || !ruanganSel || !jenisSel) return;
            function applyDependents() {
                var rsId = rsSel.value;
                var allowedRuangan = rsId && ruanganByRs[rsId] ? ruanganByRs[rsId].map(String) : null;
                var allowedJenis = rsId && jenisByRs[rsId] ? jenisByRs[rsId].map(String) : null;
                [[ruanganSel, allowedRuangan], [jenisSel, allowedJenis]].forEach(function ([sel, allowed]) {
                    var current = sel.value;
                    sel.querySelectorAll('option').forEach(function (opt) {
                        opt.hidden = !!allowed && opt.value !== '' && !allowed.includes(opt.value);
                    });
                    if (allowed && current !== '' && !allowed.includes(current)) sel.value = '';
                });
            }
            rsSel.addEventListener('change', applyDependents);
            applyDependents();
        })();
    </script>
    <style>
        .top-x-scroll { height: 14px; }
        .top-x-scroll > div { height: 1px; }
    </style>
    <script>
        // ponytail: scrollbar horizontal atas yang tersinkron dengan scroll
        // tabel (bawaan browser hanya ada di bawah). Geser salah satu,
        // yang lain ikut — dua arah.
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

