<x-layouts::app>
    <x-breadcrumb :items="[['url'=>route('opname.getTable'),'label'=>'Opname'],['url'=>'','label'=>'Sync '.$model->opname_id]]" />
    <div class="content mt-4 space-y-4">
        <x-card label="Sync Opname {{ $model->opname_id }} — {{ $model->hasRs?->rs_nama }}">
            <p class="text-sm mb-3">Total: {{ $total }} | Ketemu: {{ $ketemu }} | Hilang: {{ $hilang }}</p>
            <form method="POST" action="{{ route('opname.sync.post', $model->opname_id) }}">
                @csrf
                <x-textarea label="RFID (satu per baris)" name="rfid_text" rows="8" placeholder="TEST1&#10;TEST2" />
                <x-button type="submit">Sync</x-button>
            </form>
        </x-card>
    </div>
</x-layouts::app>
