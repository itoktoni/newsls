<?php /** @var App\Models\TransaksiMaster $master */ ?>

<x-layouts::app>
    <x-breadcrumb :items="[['url' => moduleRoute('getTable'), 'label' => moduleLabel()], ['url' => '', 'label' => $master->master_kode]]" />

    <div class="content mt-4 lg:mt-0 space-y-4">
        <x-card :label="'Master ' . $master->master_kode">
            <div class="grid grid-cols-2 md:grid-cols-3 gap-4 text-sm col-span-12">
                <div>
                    <p class="text-[11px] uppercase tracking-wide text-on-surface-variant">Kode Transaksi</p>
                    <p class="font-mono font-bold">{{ $master->master_kode }}</p>
                </div>
                <div>
                    <p class="text-[11px] uppercase tracking-wide text-on-surface-variant">Rumah Sakit</p>
                    <p class="font-medium">{{ $master->hasRs?->rs_nama ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-[11px] uppercase tracking-wide text-on-surface-variant">Operator</p>
                    <p class="font-medium">{{ $master->hasOperator?->name ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-[11px] uppercase tracking-wide text-on-surface-variant">Status Transaksi</p>
                    <p>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $master->master_status === 'KOTOR' ? 'bg-amber-100 text-amber-800' : ($master->master_status === 'REWASH' ? 'bg-blue-100 text-blue-800' : 'bg-red-100 text-red-800') }}">
                            {{ $master->master_status }}
                        </span>
                    </p>
                </div>
                <div>
                    <p class="text-[11px] uppercase tracking-wide text-on-surface-variant">Tanggal Transaksi</p>
                    <p class="font-medium">{{ formatDate($master->master_tanggal) ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-[11px] uppercase tracking-wide text-on-surface-variant">Total Qty</p>
                    <p class="font-medium">{{ $master->master_qty !== null ? formatQty($master->master_qty).' kg' : '-' }} ({{ $details->total() }} baris detail)</p>
                </div>
                <div>
                    <p class="text-[11px] uppercase tracking-wide text-on-surface-variant">Berat Kotor (Timbangan)</p>
                    <p class="font-medium">{{ $master->master_qty !== null ? formatQty($master->master_qty).' kg' : 'belum ditimbang' }}</p>
                </div>
                <div>
                    <p class="text-[11px] uppercase tracking-wide text-on-surface-variant">Berat Bersih (Σ Jenis × Berat)</p>
                    <p class="font-medium">{{ formatQty($beratBersih ?? 0).' kg' }}</p>
                </div>
                <div>
                    <p class="text-[11px] uppercase tracking-wide text-on-surface-variant">Selisih (Kotor − Bersih)</p>
                    <p class="font-medium">{{ $master->master_qty !== null ? formatQty($master->master_qty - ($beratBersih ?? 0)).' kg' : '-' }}</p>
                </div>
            </div>
        </x-card>

        <x-table>
            <x-slot:head>
                <th>No.</th>
                <th>NO. RFID</th>
                <th>LINEN</th>
                <th class="num">BERAT (KG)</th>
                <th>RUMAH SAKIT</th>
                <th>RUANGAN</th>
                <th>LOKASI SCAN</th>
                <th>STATUS</th>
            </x-slot:head>

            <x-slot:body>
                @foreach($details as $i => $row)
                <tr>
                    <td>{{ $details->firstItem() + $i }}</td>
                    <td class="font-mono text-xs">{{ $row->transaksi_rfid }}</td>
                    <td>{{ $row->linen_nama ?? '-' }}</td>
                    <td class="num">{{ $row->jenis_berat !== null ? formatQty($row->jenis_berat) : '-' }}</td>
                    <td>{{ $row->rs_ori_nama ?? $row->transaksi_rs_ori ?? '-' }}</td>
                    <td>{{ $row->ruangan_nama ?? '-' }}</td>
                    <td>{{ $row->rs_scan_nama ?? $row->transaksi_rs_scan ?? '-' }}</td>
                    <td>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $row->transaksi_status === 'KOTOR' ? 'bg-amber-100 text-amber-800' : ($row->transaksi_status === 'REWASH' ? 'bg-blue-100 text-blue-800' : 'bg-red-100 text-red-800') }}">
                            {{ $row->transaksi_status }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </x-slot:body>

            <x-slot:mobile>
                <div class="p-3 space-y-3" id="mBody">
                    @foreach($details as $row)
                    <div class="border border-outline-variant rounded-xl p-4 bg-surface-container-lowest shadow-sm">
                        <div class="flex items-center gap-2 mb-2">
                            <p class="flex-1 min-w-0 text-sm font-bold font-mono text-on-surface truncate">{{ $row->transaksi_rfid }}</p>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $row->transaksi_status === 'KOTOR' ? 'bg-amber-100 text-amber-800' : ($row->transaksi_status === 'REWASH' ? 'bg-blue-100 text-blue-800' : 'bg-red-100 text-red-800') }}">{{ $row->transaksi_status }}</span>
                        </div>
                        <div class="grid grid-cols-2 gap-x-3 gap-y-2 text-xs">
                            <div>
                                <p class="text-[10px] text-on-surface-variant uppercase tracking-wide mb-0.5">LINEN</p>
                                <p class="font-medium text-on-surface truncate">{{ $row->linen_nama ?? '-' }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-[10px] text-on-surface-variant uppercase tracking-wide mb-0.5">RUANGAN</p>
                                <p class="font-medium text-on-surface truncate">{{ $row->ruangan_nama ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-[10px] text-on-surface-variant uppercase tracking-wide mb-0.5">RUMAH SAKIT</p>
                                <p class="font-medium text-on-surface truncate">{{ $row->rs_ori_nama ?? $row->transaksi_rs_ori ?? '-' }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-[10px] text-on-surface-variant uppercase tracking-wide mb-0.5">LOKASI SCAN</p>
                                <p class="font-medium text-on-surface truncate">{{ $row->rs_scan_nama ?? $row->transaksi_rs_scan ?? '-' }}</p>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </x-slot:mobile>
        </x-table>

        <x-pagination :paginator="$details" />
    </div>
</x-layouts::app>
