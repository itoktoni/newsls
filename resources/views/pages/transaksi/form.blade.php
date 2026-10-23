<?php /** @var App\Models\TransaksiMaster $model */ ?>

<x-layouts::app>
    <x-breadcrumb :items="[['url' => moduleRoute('getTable'), 'label' => moduleLabel()], ['url' => '', 'label' => isset($model) && $model->exists ? 'Update' : 'Create']]" />

    <x-form :model="$model">
        <x-card :label="moduleLabel()">
            @bind($model ?? null)

                <x-input col="6" name="master_kode" label="Kode Transaksi" />
                <x-select col="6" name="master_rs_id" label="Rumah Sakit" :options="$rs" />
                <x-select col="6" name="master_status" label="Status Transaksi" :options="$status" />
                <x-input col="3" name="master_tanggal" label="Tanggal Transaksi" type="date" />
                <x-input col="3" name="master_qty" label="Berat Kotor Timbangan (Kg)" type="number" step="0.001" min="0" />

            @endbind
        </x-card>

        <x-action :model="$model" :action="['save']"/>
    </x-form>
</x-layouts::app>
