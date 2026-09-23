<x-layout>
    <x-card>
        <x-form :model="$model" :spa="false" target="_blank"  method="GET" action="{{ moduleRoute('getPrint') }}" :upload="true">

            <x-action form="print">
                <x-button type="submit" class="btn btn-success" label="Export" name="action" value="export"/>
            </x-action>

            <input type="hidden" name="report_name" value="{{ moduleName() }}">

            <livewire:dropdownrs />

            <x-form-input col="6" type="date" label="Tanggal Awal" name="start_rekap" />
            <x-form-input col="6" type="date" label="Tanggal Akhir" name="end_rekap" />

        @endbind

        </x-form>
    </x-card>
</x-layout>
