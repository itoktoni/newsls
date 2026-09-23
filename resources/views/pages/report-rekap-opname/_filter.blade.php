{{-- Filter Report Opname — shared semua table report opname --}}
@php
    $reportTitle = $title ?? 'Report Opname';
    $showJenis = $showJenis ?? false;
    $jenisOptions = $jenisOptions ?? [];
@endphp

<form method="GET" action="{{ moduleRoute('getPrint') }}" target="_blank">
    <x-card :label="'Filter '.$reportTitle" :icon="'filter_alt'">
        <x-select name="opname_id" label="Opname" col="6" :options="$opnameOptions" :default="request('opname_id')" placeholder="-- Pilih Opname --" />
        @if ($showJenis)
            <x-select name="jenis_id" label="Jenis Linen" col="6" :options="$jenisOptions" :default="request('jenis_id')" placeholder="-- Semua Jenis --" />
        @endif
    </x-card>

    <div class="no-print print:hidden">
        <x-action :model="null" :action="['print']" :cancel="moduleRoute('getTable')">
            <x-slot:right>
                <button type="submit"
                    class="inline-flex items-center justify-center gap-1 h-8 md:h-10 px-2.5 md:px-4 text-xs md:text-sm font-semibold rounded-lg bg-primary text-on-primary hover:bg-primary/90 shadow-sm transition-all active:scale-95 shrink-0">
                    <span class="material-symbols-outlined text-base md:text-xl">print</span>
                    <span class="hidden sm:inline">Tampilkan</span>
                </button>
                <button type="submit" formaction="{{ moduleRoute('getExportExcel') }}" formtarget="_self"
                    class="inline-flex items-center justify-center gap-1 h-8 md:h-10 px-2.5 md:px-4 text-xs md:text-sm font-semibold rounded-lg bg-green-600 text-white hover:opacity-90 shadow-sm transition-all active:scale-95 shrink-0">
                    <span class="material-symbols-outlined text-base md:text-xl">download</span>
                    <span class="hidden sm:inline">Export Excel</span>
                </button>
            </x-slot:right>
        </x-action>
    </div>
    <style>
        @media print { .no-print { display: none !important; } }
    </style>
</form>
