<?php /** @var App\Models\Users $model */ ?>

<x-layouts::app>
    <x-breadcrumb :items="[['url' => moduleRoute('getTable'), 'label' => moduleLabel()], ['url' => '', 'label' => isset($model) && $model->exists ? 'Update' : 'Create']]" />

    <x-form :model="$model" enctype="multipart/form-data">
        <x-card :label="moduleLabel()">
            @bind($model ?? null)

                <x-input col="6" name="name" />
                <x-input col="6" name="email" />
                <x-input col="6" type="password" name="password" />
                <x-select col="6" name="role" :options="$role"/>

                <x-file
                    name="avatar"
                    label="Foto Profil"
                    col="12"
                    accept="image/*"
                    capture="environment"
                    :preview="true"
                    :value="$model?->avatar_url"
                    helper="Ambil foto via kamera di HP atau pilih dari galeri" />

                {{-- ponytail: hak akses RS (pivot rs_dan_user) — kosongkan = semua RS. --}}
                @php
                    $selectedRs = array_map('strval', (array) old('rs_ids', isset($selectedRsIds) ? $selectedRsIds : []));
                @endphp
                <div class="col-span-12">
                    <label class="font-body-sm text-body-sm font-bold text-on-surface-variant block mb-1">
                        Akses Rumah Sakit <span class="font-normal">({{ count($selectedRs) }} dipilih, kosong = semua)</span>
                    </label>
                    <input type="search" id="rs-search" placeholder="Cari rumah sakit..."
                        autocomplete="off"
                        class="w-full md:w-1/3 mb-2 px-3 py-2 text-sm border border-outline-variant rounded-lg focus:border-primary focus:ring-1 focus:ring-primary outline-none bg-white">
                    <div id="rs-grid" class="grid grid-cols-1 md:grid-cols-3 gap-1 max-h-64 overflow-y-auto border border-outline-variant rounded-lg p-3 bg-white">
                        @foreach($allRs as $id => $nama)
                        <label class="flex items-center gap-2 cursor-pointer py-0.5" data-name="{{ strtolower($nama) }}">
                            <input type="checkbox" name="rs_ids[]" value="{{ $id }}"
                                class="w-4 h-4 rounded border-outline-variant text-primary focus:ring-primary-container"
                                {{ in_array((string) $id, $selectedRs, true) ? 'checked' : '' }}>
                            <span class="text-sm text-on-surface-variant">{{ $nama }}</span>
                        </label>
                        @endforeach
                    </div>
                    @error('rs_ids')<span class="font-label-caps text-label-caps text-error mt-1 block">{{ $message }}</span>@enderror
                </div>
                <script>
                    (function () {
                        var s = document.getElementById('rs-search'), g = document.getElementById('rs-grid');
                        if (!s || !g) return;
                        s.addEventListener('input', function () {
                            var q = s.value.toLowerCase();
                            g.querySelectorAll('label[data-name]').forEach(function (el) {
                                el.style.display = el.dataset.name.includes(q) ? '' : 'none';
                            });
                        });
                    })();
                </script>

                {{-- Form tampil mobile (pivot mobile_menu_dan_user) — kosongkan = semua menu. --}}
                @php
                    $selectedMenus = array_map('strval', (array) old('menu_ids', isset($selectedMenuIds) ? $selectedMenuIds : []));
                @endphp
                <div class="col-span-12">
                    <label class="font-body-sm text-body-sm font-bold text-on-surface-variant block mb-1">
                        Menu Mobile <span class="font-normal">({{ count($selectedMenus) }} dipilih, kosong = semua)</span>
                    </label>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-1 max-h-64 overflow-y-auto border border-outline-variant rounded-lg p-3 bg-white">
                        @foreach($allMobileMenus as $id => $nama)
                        <label class="flex items-center gap-2 cursor-pointer py-0.5">
                            <input type="checkbox" name="menu_ids[]" value="{{ $id }}"
                                class="w-4 h-4 rounded border-outline-variant text-primary focus:ring-primary-container"
                                {{ in_array((string) $id, $selectedMenus, true) ? 'checked' : '' }}>
                            <span class="text-sm text-on-surface-variant">{{ $nama }}</span>
                        </label>
                        @endforeach
                    </div>
                    @error('menu_ids')<span class="font-label-caps text-label-caps text-error mt-1 block">{{ $message }}</span>@enderror
                </div>

            @endbind
        </x-card>

        <x-action :model="$model" :action="['save']"/>
    </x-form>
</x-layouts::app>
