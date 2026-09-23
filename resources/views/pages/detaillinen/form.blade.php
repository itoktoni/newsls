<?php /** @var App\Models\DetailLinen $model */ ?>

<x-layouts::app>
    <x-breadcrumb :items="[['url' => moduleRoute('getTable'), 'label' => moduleLabel()], ['url' => '', 'label' => isset($model) && $model->exists ? 'Update' : 'Create']]" />

    <x-form :model="$model">
        <x-card :label="moduleLabel()">
            @bind($model ?? null)

                <x-input col="6" name="detail_rfid" label="RFID" />
                <x-select col="6" name="detail_status_kepemilikan" label="Kepemilikan" :options="$milik" />
                <x-select col="6" name="detail_id_rs" label="Dipakai RS Sekarang" :options="$rs"
                    helper="Current: RS yang sedang memegang linen ini (detail_linen)." />
                <x-select col="6" name="detail_id_ruangan" label="Ruangan" :options="$ruangan" />
                <x-select col="6" name="detail_id_jenis" label="Jenis Linen" :options="$jenis" />
                <x-select col="6" name="detail_id_bahan" label="Bahan" :options="$bahan" />
                <x-select col="6" name="detail_id_supplier" label="Supplier" :options="$supplier" />
                <x-select col="6" name="detail_status_cuci" label="Status Cuci" :options="$cuci" />
                <x-input col="6" name="detail_tgl_cek" label="Tanggal Cek" type="date" />
                <x-textarea col="12" name="detail_deskripsi" label="Deskripsi" />

                {{-- ponytail: MASTER pemilik (config_linen) —
                     DEDICATED = single RS (dropdown), GROUP = multi RS (checklist
                     min 2), FREE = disembunyikan (nol baris config = bebas).
                     Deskripsi user: DEDICATED cukup single select, GROUP/FREE
                     checklist — di sini FREE tetap hidden sesuai logika
                     master (0 baris); GROUP yang checklist. --}}
                @php
                    $selectedRs = array_map('strval', (array) old('rs_ids', isset($selectedRs) ? $selectedRs : []));
                    $milikVal = old('detail_status_kepemilikan', $model->detail_status_kepemilikan ?? 'DEDICATED');
                    // ponytail: Dedicated single-select butuh 1 nilai; old array
                    // diambil elemen pertama untuk pre-select.
                    $selectedRsSingle = old('rs_ids', isset($selectedRs) ? $selectedRs : []);
                    $selectedRsSingle = is_array($selectedRsSingle) ? ($selectedRsSingle[0] ?? '') : $selectedRsSingle;
                    $selectedRsSingle = (string) $selectedRsSingle;
                @endphp
                <div class="col-span-12" id="rs-picker-dedicated" style="{{ $milikVal === 'DEDICATED' ? '' : 'display:none' }}">
                    <label class="font-body-sm text-body-sm font-bold text-on-surface-variant block mb-1">
                        RS Pemilik (Master)
                    </label>
                    <select name="rs_ids" class="w-full h-12 pl-4 pr-10 bg-white border border-outline-variant rounded-lg font-body-sm appearance-none cursor-pointer focus:border-primary-container focus:ring-1 focus:ring-primary-container outline-none transition-all">
                        <option value="">-- Pilih RS Pemilik --</option>
                        @foreach($rs as $id => $nama)
                        <option value="{{ $id }}" {{ (string) $id === $selectedRsSingle ? 'selected' : '' }}>{{ $nama }}</option>
                        @endforeach
                    </select>
                    @error('rs_ids')<span class="font-label-caps text-label-caps text-error mt-1 block">{{ $message }}</span>@enderror
                </div>
                <div class="col-span-12" id="rs-picker-group" style="{{ $milikVal === 'GROUP' ? '' : 'display:none' }}">
                    <label class="font-body-sm text-body-sm font-bold text-on-surface-variant block mb-1">
                        RS Pemilik (Master) <span class="font-normal">(GROUP: min 2 RS — mis. siloam A + siloam B)</span>
                    </label>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-1 max-h-64 overflow-y-auto border border-outline-variant rounded-lg p-3 bg-white">
                        @foreach($rs as $id => $nama)
                        <label class="flex items-center gap-2 cursor-pointer py-0.5">
                            <input type="checkbox" name="rs_ids[]" value="{{ $id }}"
                                class="w-4 h-4 rounded border-outline-variant text-primary focus:ring-primary-container"
                                {{ in_array((string) $id, $selectedRs, true) ? 'checked' : '' }}>
                            <span class="text-sm text-on-surface-variant">{{ $nama }}</span>
                        </label>
                        @endforeach
                    </div>
                    @error('rs_ids')<span class="font-label-caps text-label-caps text-error mt-1 block">{{ $message }}</span>@enderror
                </div>

            @endbind
        </x-card>

        <x-action :model="$model" :action="['save']" />
    </x-form>

    {{-- ponytail: rantai ganti RFID (A→B→C) — tercatat otomatis tiap RFID diubah. --}}
    @if(!empty($history) && count($history))
    <x-card label="Riwayat Ganti RFID">
        <div class="col-span-12 overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-on-surface-variant border-b border-outline-variant">
                        <th class="py-2 pr-4 font-semibold">RFID Lama</th>
                        <th class="py-2 pr-4 font-semibold">RFID Baru</th>
                        <th class="py-2 pr-4 font-semibold">Tanggal Ganti</th>
                        <th class="py-2 font-semibold">Diganti Oleh</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($history as $h)
                    <tr class="border-b border-outline-variant/50 last:border-b-0">
                        <td class="py-2 pr-4 font-mono text-xs">{{ $h->ganti_rfid_lama }}</td>
                        <td class="py-2 pr-4 font-mono text-xs font-semibold text-primary">{{ $h->ganti_rfid_baru }}</td>
                        <td class="py-2 pr-4 whitespace-nowrap">{{ formatDate($h->ganti_tanggal, true) ?? '-' }}</td>
                        <td class="py-2">{{ $h->hasUser?->name ?? ($h->ganti_by ? '#'.$h->ganti_by : '-') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </x-card>
    @endif

    <script>
        (function () {
            var milik = document.querySelector('select[name="detail_status_kepemilikan"]');
            var dedicated = document.getElementById('rs-picker-dedicated');
            var group = document.getElementById('rs-picker-group');
            if (!milik || !dedicated || !group) return;
            function toggle() {
                var v = milik.value;
                dedicated.style.display = v === 'DEDICATED' ? '' : 'none';
                group.style.display = v === 'GROUP' ? '' : 'none';
                var dSel = dedicated.querySelector('select');
                var gChecks = group.querySelectorAll('input[type="checkbox"]');
                if (dSel) dSel.disabled = v !== 'DEDICATED';
                gChecks.forEach(function (c) { c.disabled = v !== 'GROUP'; });
            }
            milik.addEventListener('change', toggle);
            toggle();
        })();
    </script>
</x-layouts::app>

