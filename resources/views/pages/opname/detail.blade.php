<?php /** @var App\Models\Opname $opname */ ?>

<x-layouts::app>
    <x-breadcrumb :items="[
        ['url' => route('opname.getTable'), 'label' => 'Opname'],
        ['url' => '', 'label' => 'Detail #'.$opname->opname_id],
    ]" />

    <div class="content mt-4 lg:mt-0 space-y-4">
        <x-card label="Detail Opname #{{ $opname->opname_id }} — {{ $opname->opname_nama }}" icon="fact_check">
            <div class="col-span-12 md:col-span-4">
                <p class="text-[10px] text-on-surface-variant uppercase tracking-wide mb-0.5">Rumah Sakit</p>
                <p class="font-medium text-primary">{{ $opname->hasRs?->rs_nama ?? $opname->opname_id_rs }}</p>
            </div>
            <div class="col-span-12 md:col-span-3">
                <p class="text-[10px] text-on-surface-variant uppercase tracking-wide mb-0.5">Periode</p>
                <p class="font-medium text-on-surface">{{ $opname->opname_mulai?->format('Y-m-d') ?? '-' }} → {{ $opname->opname_selesai?->format('Y-m-d') ?? '-' }}</p>
            </div>
            <div class="col-span-12 md:col-span-2">
                <p class="text-[10px] text-on-surface-variant uppercase tracking-wide mb-0.5">Capture</p>
                <p class="font-medium {{ $opname->opname_capture ? 'text-green-700' : 'text-amber-700' }}">
                    {{ $opname->opname_capture ? \Carbon\Carbon::parse($opname->opname_capture)->format('Y-m-d H:i') : 'Belum capture' }}
                </p>
            </div>
            <div class="col-span-6 md:col-span-1">
                <p class="text-[10px] text-on-surface-variant uppercase tracking-wide mb-0.5">Total</p>
                <p class="font-bold text-on-surface">{{ $total }}</p>
            </div>
            <div class="col-span-6 md:col-span-1">
                <p class="text-[10px] text-on-surface-variant uppercase tracking-wide mb-0.5">Ketemu</p>
                <p class="font-bold text-green-700">{{ $ketemu }}</p>
            </div>
            <div class="col-span-6 md:col-span-1">
                <p class="text-[10px] text-on-surface-variant uppercase tracking-wide mb-0.5">Hilang</p>
                <p class="font-bold text-red-600">{{ $hilang }}</p>
            </div>
        </x-card>

        <x-card label="List RFID" icon="list_alt">
            @if ($data->isEmpty())
                <div class="col-span-12 py-8 text-center text-sm text-on-surface-variant">
                    @if (empty($opname->opname_capture))
                        Belum ada capture — klik tombol <strong>Capture</strong> di table Opname dulu.
                    @else
                        Tidak ada RFID untuk opname ini.
                    @endif
                </div>
            @else
                <div class="col-span-12 overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="text-[11px] uppercase text-on-surface-variant border-b border-outline-variant">
                            <tr>
                                <th class="py-2 pr-3">No.</th>
                                <th class="py-2 pr-3">RFID</th>
                                <th class="py-2 pr-3">Jenis Linen</th>
                                <th class="py-2 pr-3">Ruangan</th>
                                <th class="py-2 pr-3">RS</th>
                                <th class="py-2 pr-3">Pemakaian</th>
                                <th class="py-2 pr-3">Tgl Terakhir</th>
                                <th class="py-2 pr-3">Transaksi</th>
                                <th class="py-2 pr-3">Proses</th>
                                <th class="py-2 pr-3">Status Linen</th>
                                <th class="py-2 pr-3">Scan Opname</th>
                                <th class="py-2 pr-3">Waktu Scan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($data as $row)
                            @php $view = $row->hasView; @endphp
                            <tr class="border-b border-outline-variant/50 hover:bg-surface-container-low">
                                <td class="py-2 pr-3 text-on-surface-variant">{{ $data->firstItem() + $loop->index }}</td>
                                <td class="py-2 pr-3 font-mono text-xs font-semibold">{{ $row->opname_detail_rfid }}</td>
                                <td class="py-2 pr-3">{{ $view?->hasJenis?->jenis_nama ?? '-' }}</td>
                                <td class="py-2 pr-3">{{ $view?->hasRuangan?->ruangan_nama ?? '-' }}</td>
                                <td class="py-2 pr-3">{{ $view?->hasRs?->rs_nama ?? $view?->detail_id_rs ?? '-' }}</td>
                                <td class="py-2 pr-3">{{ $view?->detail_total_bersih ?? 0 }}</td>
                                <td class="py-2 pr-3 text-xs">{{ $view?->detail_updated_at ? formatDate($view->detail_updated_at, true) : '-' }}</td>
                                <td class="py-2 pr-3">{{ $row->opname_detail_transaksi ?: '-' }}</td>
                                <td class="py-2 pr-3">{{ $row->opname_detail_proses ?: 'Belum Register' }}</td>
                                <td class="py-2 pr-3">{{ $view?->detail_status_linen ?? '-' }}</td>
                                <td class="py-2 pr-3">
                                    @if ((int) $row->opname_detail_ketemu === 1)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Sudah Opname</span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">Belum</span>
                                    @endif
                                </td>
                                <td class="py-2 pr-3 text-xs">{{ (int) $row->opname_detail_ketemu === 1 && $row->opname_detail_waktu ? formatDate($row->opname_detail_waktu, true) : '-' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- mobile cards --}}
                <div class="col-span-12 space-y-3 md:hidden mt-3">
                    @foreach ($data as $row)
                    @php $view = $row->hasView; @endphp
                    <div class="border border-outline-variant rounded-xl p-4 bg-surface-container-lowest">
                        <div class="flex items-start justify-between gap-2 mb-2">
                            <p class="text-sm font-bold font-mono text-on-surface truncate">{{ $row->opname_detail_rfid }}</p>
                            @if ((int) $row->opname_detail_ketemu === 1)
                                <span class="shrink-0 inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-green-100 text-green-800">Sudah Opname</span>
                            @else
                                <span class="shrink-0 inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-amber-100 text-amber-800">Belum</span>
                            @endif
                        </div>
                        <div class="grid grid-cols-2 gap-x-3 gap-y-2 text-xs">
                            <div>
                                <p class="text-[10px] text-on-surface-variant uppercase">Jenis</p>
                                <p class="font-medium">{{ $view?->hasJenis?->jenis_nama ?? '-' }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-[10px] text-on-surface-variant uppercase">Ruangan</p>
                                <p class="font-medium">{{ $view?->hasRuangan?->ruangan_nama ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-[10px] text-on-surface-variant uppercase">RS</p>
                                <p class="font-medium">{{ $view?->hasRs?->rs_nama ?? '-' }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-[10px] text-on-surface-variant uppercase">Proses</p>
                                <p class="font-medium">{{ $row->opname_detail_proses ?: 'Belum Register' }}</p>
                            </div>
                            <div>
                                <p class="text-[10px] text-on-surface-variant uppercase">Transaksi</p>
                                <p class="font-medium">{{ $row->opname_detail_transaksi ?: '-' }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-[10px] text-on-surface-variant uppercase">Pemakaian</p>
                                <p class="font-medium">{{ $view?->detail_total_bersih ?? 0 }}</p>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <div class="col-span-12 mt-4">
                    <x-pagination :paginator="$data" />
                </div>
            @endif
        </x-card>
    </div>
</x-layouts::app>
