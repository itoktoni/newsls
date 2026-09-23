<?php /** @var App\Models\ConfigLinen $model */ ?>

<x-layouts::app>
    <x-breadcrumb :items="[['url' => moduleRoute('getTable'), 'label' => moduleLabel()], ['url' => '', 'label' => isset($model) && $model->exists ? 'Update' : 'Create']]" />

    <x-form :model="$model">
        <x-card :label="moduleLabel()">
            @bind($model ?? null)

                {{-- ponytail: tambah satu pasangan RFID + RS; pindah banyak RS sekaligus
                     lewat form Data Linen (checkbox RS Pemilik). --}}
                <x-select col="6" name="detail_rfid" label="RFID" :options="$rfid" class="search" />
                <x-select col="6" name="rs_id" label="Rumah Sakit" :options="$rs" class="search" />
                <p class="col-span-12 text-xs text-on-surface-variant -mt-2">
                    Baris di sini = <strong>master kepemilikan</strong> (RFID ini milik RS mana).
                    Linen FREE tidak butuh baris config — bebas dipakai RS lain kecuali milik DEDICATED/GROUP.
                    DEDICATED min 1 RS, GROUP min 2 RS (mis. siloam A + siloam B bisa tukar).
                    Kolom "Dipakai Sekarang" di tabel berasal dari detail_linen (current holder), bukan dari sini.
                </p>

            @endbind
        </x-card>

        <x-action :model="$model" :action="['save']" />
    </x-form>
</x-layouts::app>
