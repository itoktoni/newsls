<?php /** @var App\Models\Rs $rsRecord */ ?>

<x-layouts::app>
    <x-breadcrumb :items="[['url' => moduleRoute('getTable'), 'label' => moduleLabel()], ['url' => '', 'label' => 'Parstock — '.$rsRecord->rs_nama]]" />

    <div class="content mt-4 lg:mt-0">
        {{-- Filter jenis --}}
        <form method="GET" action="{{ moduleRoute('getParstock', ['id' => $rsRecord->rs_id]) }}" class="bg-surface-container-lowest border border-outline-variant rounded-xl p-4 mb-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                <div>
                    <label class="font-body-sm text-body-sm font-bold text-on-surface-variant block mb-1">Jenis Linen</label>
                    <input type="search" name="jenis" value="{{ request('jenis') }}" placeholder="Cari jenis..."
                        class="w-full px-3 py-2 text-sm border border-outline-variant rounded-lg focus:border-primary focus:ring-1 focus:ring-primary outline-none bg-white">
                </div>
                <div class="flex items-end gap-2">
                    <x-button variant="primary" type="submit">Cari</x-button>
                    <a href="{{ moduleRoute('getParstock', ['id' => $rsRecord->rs_id]) }}" wire:navigate>
                        <x-button variant="soft" type="button">Reset</x-button>
                    </a>
                </div>
            </div>
        </form>

        {{-- ponytail: input parstock[jenis_id], Kurang = parstock - register. --}}
        <form method="POST" action="{{ moduleRoute('postParstock', ['id' => $rsRecord->rs_id]) }}">
            @csrf

            <x-table>
                <x-slot:head>
                    <th>Jenis Linen</th>
                    <th>Parstock</th>
                    <th>Total Register</th>
                    <th>Kurang</th>
                </x-slot:head>

                <x-slot:body>
                    @forelse($rows as $row)
                    @php $kurang = $row->parstock === null ? null : $row->parstock - $row->total_register; @endphp
                    <tr>
                        <td>{{ $row->jenis_nama }}</td>
                        <td>
                            <input type="number" min="0" step="1" name="parstock[{{ $row->jenis_id }}]"
                                value="{{ old('parstock.'.$row->jenis_id, $row->parstock) }}"
                                placeholder="—"
                                class="w-28 px-2 py-1.5 text-sm border border-outline-variant rounded-lg focus:border-primary focus:ring-1 focus:ring-primary outline-none bg-white">
                            @error('parstock.'.$row->jenis_id)<span class="text-error text-xs block">{{ $message }}</span>@enderror
                        </td>
                        <td>{{ $row->total_register }}</td>
                        <td>
                            @if($kurang === null)
                            <span class="text-on-surface-variant">-</span>
                            @elseif($kurang > 0)
                            <span class="font-bold text-error">{{ $kurang }}</span>
                            @else
                            <span class="text-on-surface-variant">{{ $kurang }}</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center text-on-surface-variant">Belum ada jenis untuk RS ini. Tambahkan lewat form edit RS.</td>
                    </tr>
                    @endforelse
                </x-slot:body>

                <x-slot:mobile>
                    <div class="p-3 space-y-3">
                        @forelse($rows as $row)
                        @php $kurang = $row->parstock === null ? null : $row->parstock - $row->total_register; @endphp
                        <div class="border border-outline-variant rounded-xl p-4 bg-surface-container-lowest shadow-sm">
                            <p class="text-sm font-bold text-on-surface truncate mb-3">{{ $row->jenis_nama }}</p>
                            <div class="grid grid-cols-3 gap-2 text-xs">
                                <div>
                                    <p class="text-[10px] text-on-surface-variant uppercase tracking-wide mb-0.5">Register</p>
                                    <p class="font-bold text-on-surface">{{ $row->total_register }}</p>
                                </div>
                                <div>
                                    <p class="text-[10px] text-on-surface-variant uppercase tracking-wide mb-0.5">Kurang</p>
                                    <p class="font-bold {{ $kurang !== null && $kurang > 0 ? 'text-error' : 'text-on-surface-variant' }}">{{ $kurang ?? '-' }}</p>
                                </div>
                                <div>
                                    <p class="text-[10px] text-on-surface-variant uppercase tracking-wide mb-0.5">Parstock</p>
                                    <input type="number" min="0" step="1" name="parstock[{{ $row->jenis_id }}]"
                                        value="{{ old('parstock.'.$row->jenis_id, $row->parstock) }}"
                                        placeholder="—"
                                        class="w-full px-2 py-1.5 text-sm border border-outline-variant rounded-lg focus:border-primary outline-none bg-white">
                                </div>
                            </div>
                        </div>
                        @empty
                        <p class="text-center text-on-surface-variant text-sm py-6">Belum ada jenis untuk RS ini.</p>
                        @endforelse
                    </div>
                </x-slot:mobile>
            </x-table>

            <div class="h-20"></div>

            <x-action :model="$model" :action="['save']" :cancel="moduleRoute('getTable')">
                <span class="text-sm text-on-surface-variant whitespace-nowrap">{{ count($rows) }} jenis</span>
            </x-action>
        </form>
    </div>
</x-layouts::app>
