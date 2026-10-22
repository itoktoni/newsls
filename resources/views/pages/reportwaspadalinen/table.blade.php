<x-layouts::app>
    <x-breadcrumb :items="[['url' => '/dashboard', 'label' => 'Home'], ['url' => '', 'label' => moduleLabel()]]" />
    <div class="content mt-4 lg:mt-0 space-y-4">

        <form method="GET" action="{{ moduleRoute('getPrint') }}" target="_blank">
            <x-card :label="'Filter Report Linen Waspada'" :icon="'filter_alt'">
                <x-select name="rs_id" label="Rumah Sakit" col="4" :options="$rsOptions" :default="request('rs_id')" placeholder="-- Semua RS --" />
                <x-select name="ruangan_id" label="Ruangan" col="4" :options="$ruanganOptions" :default="request('ruangan_id')" placeholder="-- Semua Ruangan --" />
                <x-select name="jenis_id" label="Jenis Linen" col="4" :options="$jenisOptions" :default="request('jenis_id')" placeholder="-- Semua Jenis --" />
                <x-select name="tingkat" label="Tingkat Perhatian" col="4" :options="$tingkatOptions" :default="request('tingkat')" placeholder="-- Semua --" />
                <div class="col-span-12 md:col-span-4">
                    <label class="font-body-sm text-body-sm font-bold text-on-surface-variant block mb-1">Cari RFID</label>
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Sebagian RFID..."
                        class="w-full h-12 px-4 bg-white border border-outline-variant rounded-lg font-body-sm focus:border-primary-container focus:ring-1 focus:ring-primary-container outline-none transition-all" />
                </div>
                <p class="col-span-12 text-xs text-on-surface-variant">Menampilkan linen dengan pemakaian mendekati atau mencapai batas. Waspada = mendekati batas, Prioritas = mencapai batas.</p>
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
                @media print {
                    .no-print { display: none !important; }
                }
            </style>
        </form>

    </div>

    <script>
        (function () {
            var ruanganByRs = @json($ruanganByRs ?? []);
            var jenisByRs = @json($jenisByRs ?? []);
            var rsSel = document.querySelector('select[name="rs_id"]');
            var ruanganSel = document.querySelector('select[name="ruangan_id"]');
            var jenisSel = document.querySelector('select[name="jenis_id"]');
            if (!rsSel || !ruanganSel || !jenisSel) return;
            function applyDependents() {
                var rsId = rsSel.value;
                var allowedRuangan = rsId && ruanganByRs[rsId] ? ruanganByRs[rsId].map(String) : null;
                var allowedJenis = rsId && jenisByRs[rsId] ? jenisByRs[rsId].map(String) : null;
                [[ruanganSel, allowedRuangan], [jenisSel, allowedJenis]].forEach(function (pair) {
                    var sel = pair[0], allowed = pair[1];
                    var current = sel.value;
                    sel.querySelectorAll('option').forEach(function (opt) {
                        opt.hidden = !!allowed && opt.value !== '' && !allowed.includes(opt.value);
                    });
                    if (allowed && current !== '' && !allowed.includes(current)) sel.value = '';
                });
            }
            rsSel.addEventListener('change', applyDependents);
            applyDependents();
        })();
    </script>
</x-layouts::app>
