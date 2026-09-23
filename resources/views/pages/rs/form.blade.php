<?php /** @var App\Models\Rs $model */ ?>

<x-layouts::app>
    <x-breadcrumb :items="[['url' => moduleRoute('getTable'), 'label' => moduleLabel()], ['url' => '', 'label' => isset($model) && $model->exists ? 'Update' : 'Create']]" />

    <x-form :model="$model">
        <x-card :label="moduleLabel()">
            @bind($model ?? null)

                <x-input col="6" name="rs_nama" />
                <x-input col="6" name="rs_code" />
                <x-select col="6" name="rs_id_group" label="Group RS" :options="$group" />
                <x-input col="6" name="rs_alamat" />
                <x-select col="6" name="rs_status" :options="$status" />
                <x-input col="6" name="rs_harga_cuci" type="number" />
                <x-input col="6" name="rs_harga_sewa" type="number" />
                <x-textarea col="12" name="rs_deskripsi" />

                {{-- ponytail: pivot rs_dan_ruangan / rs_dan_jenis — dicentang di sini,
                     di-sync oleh RsController::postCreate/postUpdate. --}}
                @php
                    $selectedRuangan = array_map('strval', (array) old('ruangan_ids', isset($selectedRuangan) ? $selectedRuangan : []));
                    $selectedJenis = array_map('strval', (array) old('jenis_ids', isset($selectedJenis) ? $selectedJenis : []));
                @endphp
                <div class="col-span-12">
                    <label class="font-body-sm text-body-sm font-bold text-on-surface-variant block mb-1">
                        Ruangan <span class="font-normal">({{ count($selectedRuangan) }} dipilih)</span>
                    </label>
                    <input type="search" id="ruangan-search" placeholder="Cari ruangan..."
                        autocomplete="off"
                        class="w-full md:w-1/3 mb-2 px-3 py-2 text-sm border border-outline-variant rounded-lg focus:border-primary focus:ring-1 focus:ring-primary outline-none bg-white">
                    <div id="ruangan-grid" class="grid grid-cols-1 md:grid-cols-3 gap-1 max-h-64 overflow-y-auto border border-outline-variant rounded-lg p-3 bg-white">
                        @foreach($ruangan as $id => $nama)
                        <label class="flex items-center gap-2 cursor-pointer py-0.5" data-name="{{ strtolower($nama) }}">
                            <input type="checkbox" name="ruangan_ids[]" value="{{ $id }}"
                                class="w-4 h-4 rounded border-outline-variant text-primary focus:ring-primary-container"
                                {{ in_array((string) $id, $selectedRuangan, true) ? 'checked' : '' }}>
                            <span class="text-sm text-on-surface-variant">{{ $nama }}</span>
                        </label>
                        @endforeach
                    </div>
                    @error('ruangan_ids')<span class="font-label-caps text-label-caps text-error mt-1 block">{{ $message }}</span>@enderror
                </div>

                <div class="col-span-12">
                    <label class="font-body-sm text-body-sm font-bold text-on-surface-variant block mb-1">
                        Jenis Linen <span class="font-normal">({{ count($selectedJenis) }} dipilih)</span>
                    </label>
                    <input type="search" id="jenis-search" placeholder="Cari jenis linen..."
                        autocomplete="off"
                        class="w-full md:w-1/3 mb-2 px-3 py-2 text-sm border border-outline-variant rounded-lg focus:border-primary focus:ring-1 focus:ring-primary outline-none bg-white">
                    <div id="jenis-grid" class="grid grid-cols-1 md:grid-cols-3 gap-1 max-h-64 overflow-y-auto border border-outline-variant rounded-lg p-3 bg-white">
                        @foreach($jenis as $id => $nama)
                        <label class="flex items-center gap-2 cursor-pointer py-0.5" data-name="{{ strtolower($nama) }}">
                            <input type="checkbox" name="jenis_ids[]" value="{{ $id }}"
                                class="w-4 h-4 rounded border-outline-variant text-primary focus:ring-primary-container"
                                {{ in_array((string) $id, $selectedJenis, true) ? 'checked' : '' }}>
                            <span class="text-sm text-on-surface-variant">{{ $nama }}</span>
                        </label>
                        @endforeach
                    </div>
                    @error('jenis_ids')<span class="font-label-caps text-label-caps text-error mt-1 block">{{ $message }}</span>@enderror
                </div>

            @endbind
        </x-card>

        <x-action :model="$model" :action="['save']" />
    </x-form>

    <script>
        (function () {
            function wireSearch(inputId, gridId) {
                var input = document.getElementById(inputId);
                var grid = document.getElementById(gridId);
                if (!input || !grid) return;
                input.addEventListener('input', function () {
                    var q = input.value.trim().toLowerCase();
                    grid.querySelectorAll('label[data-name]').forEach(function (label) {
                        label.style.display = !q || label.dataset.name.includes(q) ? '' : 'none';
                    });
                });
            }
            wireSearch('ruangan-search', 'ruangan-grid');
            wireSearch('jenis-search', 'jenis-grid');
        })();
    </script>
</x-layouts::app>
