<x-layouts::app>
    <x-breadcrumb :items="[['url' => '/dashboard', 'label' => 'Home'], ['url' => '', 'label' => moduleLabel()]]" />
    <div class="content mt-4 lg:mt-0 space-y-4">
        @include('pages.report-rekap-opname._filter', [
            'title' => 'Rekap Opname',
            'showJenis' => true,
            'jenisOptions' => \App\Models\JenisLinen::orderBy('jenis_nama')->pluck('jenis_nama', 'jenis_id'),
        ])
    </div>
</x-layouts::app>
