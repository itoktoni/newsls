<?php
/** @var string $tab */
$asalLabel = fn ($v) => $v === 'REGISTER' ? 'Register' : \App\Enums\TransactionType::getDescription($v ?? '');
$asalBadge = fn ($v) => match ($v) {
    'KOTOR' => 'bg-amber-100 text-amber-800',
    'REJECT', 'RETUR' => 'bg-red-100 text-red-800',
    'REWASH' => 'bg-blue-100 text-blue-800',
    'REGISTER' => 'bg-gray-100 text-gray-800',
    default => 'bg-gray-100 text-gray-800',
};
?>

<x-layouts::app>
    <x-breadcrumb :items="[['url' => '/dashboard', 'label' => 'Home'], ['url' => '', 'label' => moduleLabel()]]" />
    <div class="content mt-4 lg:mt-0 space-y-4">

        {{-- Statistik gudang utama --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <x-card :label="$stats['gudang']->warehouse_nama ?? 'Gudang Utama'" :noGrid="true">
                <div class="text-center py-2">
                    <p class="text-3xl font-bold text-green-600">{{ number_format($stats['total_pcs'] ?? 0, 0, ',', '.') }}</p>
                    <p class="text-xs text-on-surface-variant mt-1">Pcs siap packing (substitusi per jenis berlaku semua status milik)</p>
                </div>
            </x-card>
            <x-card :label="'Stok per Jenis'" :noGrid="true">
                <div class="py-2 space-y-1 max-h-32 overflow-y-auto">
                    @forelse($stats['per_jenis'] ?? [] as $row)
                    <div class="flex items-center justify-between text-xs px-2">
                        <span class="text-on-surface truncate">{{ $row->nama }}</span>
                        <span class="font-bold text-primary">{{ number_format($row->pcs, 0, ',', '.') }}</span>
                    </div>
                    @empty
                    <p class="text-xs text-on-surface-variant text-center">Gudang kosong</p>
                    @endforelse
                </div>
            </x-card>
        </div>

        {{-- Filters --}}
        <x-filter :per-page="25" :fields="$fields">
            <x-slot:advanced>
                <x-filter-item label="RFID" name="outstanding_rfid" operator="$contains" placeholder="Sebagian RFID..." />
                <x-filter-item label="RS Scan" name="outstanding_rs_scan" :options="$rsOptions" />
                <x-filter-item label="Ruangan" name="outstanding_id_ruangan" :options="$ruanganOptions" />
                <x-filter-item label="Asal (Kotor/Retur/Rewash)" name="outstanding_status_transaksi" :options="$statusOptions" />
            </x-slot:advanced>
        </x-filter>

        {{-- Table --}}
        @php
            $currentSort = request('sort.0', '');
            $sortField = str_replace(':desc','',str_replace(':asc','',$currentSort));
            $sortDir = str_contains($currentSort, ':desc') ? 'desc' : 'asc';
        @endphp

        <div>
            <x-table>
                <x-slot:head>
                    <th>No.</th>
                    <x-table-sort field="outstanding_rfid" label="NO. RFID" :sortField="$sortField" :sortDir="$sortDir" />
                    <th>Linen</th>
                    <th>RS Scan</th>
                    <th>Ruangan</th>
                    <x-table-sort field="outstanding_status_transaksi" label="Asal" :sortField="$sortField" :sortDir="$sortDir" />
                    <th>Gudang</th>
                    <x-table-sort field="outstanding_updated_at" label="Masuk Gudang" :sortField="$sortField" :sortDir="$sortDir" />
                </x-slot:head>

                <x-slot:body>
                    @foreach($data as $i => $table)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td class="whitespace-nowrap font-mono text-xs">{{ $table->outstanding_rfid }}</td>
                        <td>{{ $table->linen_nama ?? '-' }}</td>
                        <td>{{ $table->rs_nama ?? '-' }}</td>
                        <td>{{ $table->ruangan_nama ?? '-' }}</td>
                        <td>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $asalBadge($table->outstanding_status_transaksi) }}">
                                {{ $asalLabel($table->outstanding_status_transaksi) }}
                            </span>
                        </td>
                        <td>{{ $table->gudang_nama ?? '-' }}</td>
                        <td class="whitespace-nowrap text-xs">{{ $table->outstanding_updated_at }}</td>
                    </tr>
                    @endforeach
                </x-slot:body>

                <x-slot:mobile>
                    <x-table-mobile-select :model="null" :total="$data"/>
                    <div class="p-3 space-y-3" id="mBody">
                        @foreach($data as $table)
                        <div class="border border-outline-variant rounded-xl p-4 bg-surface-container-lowest shadow-sm active:scale-[0.99] transition-transform" data-id="{{ $table->outstanding_rfid }}" onclick="mToggle(this)">
                            <div class="flex items-center gap-2 mb-2">
                                <span data-check class="icon-[tabler--circle] size-5 text-base-content/20 shrink-0"></span>
                                <p class="flex-1 min-w-0 text-sm font-bold font-mono text-on-surface truncate">{{ $table->outstanding_rfid }}</p>
                                <span class="inline-flex items-center shrink-0 px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $asalBadge($table->outstanding_status_transaksi) }}">
                                    {{ $asalLabel($table->outstanding_status_transaksi) }}
                                </span>
                            </div>
                            <div class="mb-3 rounded-lg bg-surface-container px-3 py-2 text-xs">
                                <div class="flex items-center justify-between gap-2">
                                    <span class="text-on-surface-variant shrink-0">Gudang</span>
                                    <span class="font-semibold text-primary text-right truncate">{{ $table->gudang_nama ?? '-' }}</span>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-x-3 gap-y-2 mb-3 text-xs">
                                <div class="min-w-0">
                                    <p class="text-[10px] text-on-surface-variant uppercase tracking-wide mb-0.5">Linen</p>
                                    <p class="font-medium text-on-surface truncate">{{ $table->linen_nama ?? '-' }}</p>
                                </div>
                                <div class="min-w-0 text-right">
                                    <p class="text-[10px] text-on-surface-variant uppercase tracking-wide mb-0.5">RS Scan</p>
                                    <p class="font-medium text-on-surface truncate">{{ $table->rs_nama ?? '-' }}</p>
                                </div>
                            </div>
                            <div class="flex items-center justify-between pt-2 border-t border-outline-variant/50">
                                <span class="text-[9px] font-mono text-on-surface-variant bg-surface-container px-2 py-0.5 rounded">{{ $table->outstanding_key }}</span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </x-slot:mobile>
            </x-table>
        </div>

        <x-pagination :paginator="$data" />

    </div>

    <input type="hidden" class="module" value="{{ Str::beforeLast(request()->route()->uri(), '/') }}">
    <script src="/js/table.js"></script>
    <script>initTable('{{ $sortField }}', '{{ $sortDir }}');</script>
</x-layouts::app>
