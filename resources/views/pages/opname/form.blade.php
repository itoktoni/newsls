<?php /** @var App\Models\Opname $model */ ?>

<x-layouts::app>
    <x-breadcrumb :items="[['url' => moduleRoute('getTable'), 'label' => moduleLabel()], ['url' => '', 'label' => isset($model) && $model->exists ? 'Update' : 'Create']]" />

    <x-form :model="$model ?? null">
        <x-card :label="moduleLabel()">
            @bind($model ?? null)

                <x-input col="6" label="Nama Opname" name="opname_nama" />
                <x-select col="6" label="Rumah Sakit" name="opname_id_rs" :options="$rs ?? []" />
                <x-input col="6" label="Mulai" name="opname_mulai" type="date" />
                <x-input col="6" label="Selesai" name="opname_selesai" type="date" />
                <x-select col="6" label="Status" name="opname_status" :options="$status ?? []" />

            @endbind
        </x-card>

        <x-action :model="$model ?? null" :action="['save']" />
    </x-form>
</x-layouts::app>
