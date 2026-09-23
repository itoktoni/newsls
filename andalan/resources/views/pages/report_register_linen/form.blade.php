<x-layout>
    <x-card>
        <x-form :model="$model" :spa="false" target="_blank"  method="GET" action="{{ moduleRoute('getPrint') }}" :upload="true">
            <x-action form="print" />
                <input type="hidden" name="report_name" value="{{ moduleName() }}">
                <x-form-input col="3" type="date" label="Tanggal Awal" name="start_date" />
                <x-form-input col="3" type="date" label="Tanggal Akhir" name="end_date" />
                <x-form-select col="6" class="search" name="view_rs_id" label="Rumah Sakit" :options="$rs" />
                <x-form-select col="6" class="search" name="view_ruangan_id" label="Ruangan" :options="$ruangan" />
                <x-form-select col="6" class="search" name="view_linen_id" label="Jenis Linen" :options="$jenis" />
                <x-form-select col="6" class="search" name="view_status_cuci" label="Status Cuci" :options="$cuci" />
                <x-form-select col="6" class="search" name="view_status_register" label="Status Register" :options="$register" />
            @endbind

        </x-form>
    </x-card>
</x-layout>
