<x-layout>
    <x-card>
        <x-form :model="$model" :spa="false" target="_blank"  method="GET" action="{{ moduleRoute('getPrint') }}" :upload="true">
            <x-action form="print" />
                <input type="hidden" name="report_name" value="{{ moduleName() }}">
                @livewire('dropdown-rs', ['hide' => 'ruangan'])
                <x-form-input col="6" type="date" label="Tanggal Awal" name="start_date" />
                <x-form-input col="6" type="date" label="Tanggal Akhir" name="end_date" />
            @endbind

        </x-form>
    </x-card>
</x-layout>
