<?php /** @var App\Models\Supplier $model */ ?>

<x-layouts::app>
    <x-breadcrumb :items="[['url' => moduleRoute('getTable'), 'label' => moduleLabel()], ['url' => '', 'label' => isset($model) && $model->exists ? 'Update' : 'Create']]" />

    <x-form :model="$model">
        <x-card :label="moduleLabel()">
            @bind($model ?? null)

                <x-input col="6" name="supplier_nama" />
                <x-input col="6" name="supplier_kontak" />
                <x-input col="6" name="supplier_phone" />
                <x-input col="6" name="supplier_email" type="email" />
                <x-textarea col="12" name="supplier_alamat" />

            @endbind
        </x-card>

        <x-action :model="$model" :action="['save']" />
    </x-form>
</x-layouts::app>
