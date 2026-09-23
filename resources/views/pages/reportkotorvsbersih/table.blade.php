<x-layouts::app>
    <x-breadcrumb :items="[['url' => '/dashboard', 'label' => 'Home'], ['url' => '', 'label' => moduleLabel()]]" />
    <div class="content mt-4 lg:mt-0 space-y-4">

        {{-- Form filter saja (RS + periode wajib, maks 93 hari).
             Tampilkan -> buka matriks print + Export Excel, filter terbawa sebagai query. --}}
        <form method="GET" action="{{ moduleRoute('getPrint') }}" target="_blank">
            <x-card :label="'Filter Kotor vs Bersih'" :icon="'filter_alt'">
                <x-select name="rs_id" label="Rumah Sakit" col="4" :options="$rsOptions" :default="request('rs_id')" placeholder="-- Pilih RS --" />
                <div class="col-span-12 md:col-span-4">
                    <label class="font-body-sm text-body-sm font-bold text-on-surface-variant block mb-1">Tanggal Awal</label>
                    <input type="date" name="start_date" value="{{ request('start_date') }}"
                        class="w-full h-12 px-4 bg-white border border-outline-variant rounded-lg font-body-sm focus:border-primary-container focus:ring-1 focus:ring-primary-container outline-none transition-all" />
                </div>
                <div class="col-span-12 md:col-span-4">
                    <label class="font-body-sm text-body-sm font-bold text-on-surface-variant block mb-1">Tanggal Akhir</label>
                    <input type="date" name="end_date" value="{{ request('end_date') }}"
                        class="w-full h-12 px-4 bg-white border border-outline-variant rounded-lg font-body-sm focus:border-primary-container focus:ring-1 focus:ring-primary-container outline-none transition-all" />
                </div>
            </x-card>

            {{-- Action bar standar bottom-fixed, hilang saat print --}}
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
                @media print {
                    .no-print { display: none !important; }
                }
            </style>
        </form>

    </div>
</x-layouts::app>
