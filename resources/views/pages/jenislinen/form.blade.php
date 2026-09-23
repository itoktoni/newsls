<?php /** @var App\Models\JenisLinen $model */ ?>

<x-layouts::app>
    <x-breadcrumb :items="[['url' => moduleRoute('getTable'), 'label' => moduleLabel()], ['url' => '', 'label' => isset($model) && $model->exists ? 'Update' : 'Create']]" />

    <x-form :model="$model" enctype="multipart/form-data">
        <x-card :label="moduleLabel()">
            @bind($model ?? null)

                <x-input col="6" name="jenis_nama" />
                <x-input col="6" name="jenis_berat" type="number" />
                <x-select col="6" name="jenis_id_kategori" :options="$kategori" />
                <x-select col="6" name="jenis_id_rs" :options="$rs" />
                <x-textarea col="12" name="jenis_deskripsi" />

                <x-file
                    name="jenis_gambar"
                    label="Gambar Linen"
                    col="12"
                    accept="image/*"
                    capture="environment"
                    :preview="true"
                    :value="$model?->gambar_url"
                    helper="Foto jenis linen untuk identifikasi" />

            @endbind
        </x-card>

        <x-action :model="$model" :action="['save']" />
    </x-form>
</x-layouts::app>
