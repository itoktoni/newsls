<?php /** @var App\Models\MobileMenu $model */ ?>

<x-layouts::app>
    <x-breadcrumb :items="[['url' => moduleRoute('getTable'), 'label' => moduleLabel()], ['url' => '', 'label' => isset($model) && $model->exists ? 'Update' : 'Create']]" />

    <x-form :model="$model">
        <x-card :label="moduleLabel()">
            @bind($model ?? null)

                <x-input col="6" name="mobile_menu_nama" />
                <x-input col="6" name="mobile_menu_code" helper="Kode unik untuk dicocokkan aplikasi mobile (mis. register, scan_kotor). Kosongkan bila tidak perlu." />
                <x-input col="6" type="number" name="mobile_menu_urut" helper="Urutan tampil di aplikasi mobile (kecil = duluan)." />
                <x-select col="6" name="mobile_menu_aktif" :options="[1 => 'Ya', 0 => 'Tidak']" />
                <x-textarea col="12" name="mobile_menu_deskripsi" />

            @endbind
        </x-card>

        <x-action :model="$model" :action="['save']" />
    </x-form>
</x-layouts::app>
