<x-layout>
    <x-card>
        <x-form :model="$model" :spa="false" target="_blank"  method="GET" action="{{ moduleRoute('getPrint') }}" :upload="true">
            <x-action form="print" />
                <input type="hidden" name="report_name" value="{{ moduleName() }}">
                <x-form-select col="6" class="search" name="view_rs_id" label="Rumah Sakit" :options="$rs" />
                <x-form-input col="3" type="date" label="Tanggal Kotor" name="start_pending" />
                <x-form-input col="3" type="date" label="Tanggal Akhir" name="end_pending" />
                <x-form-select col="6" class="search" name="view_ruangan_id" label="Ruangan" :options="$ruangan" />
                <x-form-input col="3" type="date" label="Tanggal Bersih" name="start_bersih" />
                <x-form-input col="3" type="date" label="Tanggal Akhir" name="end_bersih" />
                <x-form-select col="6" name="status" label="Status" :options="$transaction" />
            @endbind

        </x-form>
    </x-card>
</x-layout>
