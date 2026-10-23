<x-layout>
    <x-card>
        <x-form :model="$model" :spa="false" target="_blank"  method="GET" action="{{ moduleRoute('getPrint') }}" :upload="true">
            <x-action form="print" />
                <input type="hidden" name="report_name" value="{{ moduleName() }}">
                <x-form-select col="6" class="search" name="detail_id_rs" label="Rumah Sakit" :options="$rs" />
                <x-form-select col="6" class="search" name="detail_id_jenis" label="Nama Linen" :options="$jenis" />
            @endbind

        </x-form>
    </x-card>
</x-layout>
