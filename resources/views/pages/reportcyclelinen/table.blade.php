<x-layouts::app>
    <x-breadcrumb :items="[['url' => '/dashboard', 'label' => 'Home'], ['url' => '', 'label' => moduleLabel()]]" />
    <div class="content mt-4 lg:mt-0 space-y-4">

        {{-- Form filter saja. Tampilkan -> buka halaman print + Export Excel. --}}
        <form method="GET" action="{{ moduleRoute('getPrint') }}" target="_blank">
            <x-card :label="'Filter Cycle Linen'" :icon="'filter_alt'">
                <x-select name="rs_id" label="Rumah Sakit" col="4" :options="$rsOptions" :default="request('rs_id')" placeholder="-- Semua RS --" />
                <x-select name="hari" label="Tidak Dipakai" col="4" :options="$hariOptions" :default="request('hari', '7')" placeholder="-- Pilih --" />
                <div class="col-span-12 md:col-span-4">
                    <label class="font-body-sm text-body-sm font-bold text-on-surface-variant block mb-1">Tidak Dipakai Sejak (tanggal)</label>
                    <input type="date" name="cycle_sejak" value="{{ request('cycle_sejak') }}" max="{{ date('Y-m-d') }}"
                        class="w-full h-12 px-4 bg-white border border-outline-variant rounded-lg font-body-sm focus:border-primary-container focus:ring-1 focus:ring-primary-container outline-none transition-all" />
                </div>
            </x-card>
            <p class="text-xs text-gray-500 mt-2">Linen BERSIH di rumah sakit yang detail_report-nya lebih tua dari cutoff (default &gt; 7 hari).</p>

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
