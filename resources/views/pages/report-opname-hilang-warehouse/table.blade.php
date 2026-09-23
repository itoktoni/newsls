<x-layouts::app>
    <x-breadcrumb :items="[['url' => '/dashboard', 'label' => 'Home'], ['url' => '', 'label' => moduleLabel()]]" />
    <div class="content mt-4 lg:mt-0 space-y-4">
        @include('pages.report-rekap-opname._filter', [
            'title' => 'Hilang Warehouse',
            'showJenis' => false,
        ])
    </div>
</x-layouts::app>
