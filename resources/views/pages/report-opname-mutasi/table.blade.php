<x-layouts::app>
    <x-breadcrumb :items="[['url' => '/dashboard', 'label' => 'Home'], ['url' => '', 'label' => moduleLabel()]]" />
    <div class="content mt-4 lg:mt-0 space-y-4">
        @include('pages.report-rekap-opname._filter', [
            'title' => 'Report Opname Mutasi',
            'showJenis' => false,
        ])

        @if (! empty($rows))
            <x-card label="Ringkasan Mutasi Harian" icon="swap_horiz">
                <div class="overflow-x-auto">
                    <table class="w-full text-xs md:text-sm">
                        <thead class="text-left border-b">
                            <tr>
                                <th class="py-2 pr-3">No.</th>
                                <th class="py-2 pr-3">Tanggal</th>
                                <th class="py-2 pr-3 text-right">Register Saat SO</th>
                                <th class="py-2 pr-3 text-right">Scan Linen Terbaca</th>
                                <th class="py-2 pr-3 text-right">Belum Terbaca di Laundry</th>
                                <th class="py-2 pr-3 text-right">Linen Masih Dalam Proses</th>
                                <th class="py-2 pr-3 text-right">Total Opname</th>
                                <th class="py-2 pr-3 text-right">Linen Berchip Tidak Teridentifikasi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($rows as $row)
                                <tr class="border-b last:border-0">
                                    <td class="py-2 pr-3">{{ $row['no'] }}</td>
                                    <td class="py-2 pr-3 whitespace-nowrap">{{ $row['tanggal'] }}</td>
                                    <td class="py-2 pr-3 text-right">{{ number_format($row['register']) }}</td>
                                    <td class="py-2 pr-3 text-right">{{ number_format($row['scan']) }}</td>
                                    <td class="py-2 pr-3 text-right font-semibold">{{ number_format($row['belum']) }}</td>
                                    <td class="py-2 pr-3 text-right">{{ number_format($row['proses']) }}</td>
                                    <td class="py-2 pr-3 text-right">{{ number_format($row['total']) }}</td>
                                    <td class="py-2 pr-3 text-right">{{ number_format($row['berchip']) }}</td>
                                </tr>
                            @endforeach
                            <tr class="border-t-2 font-semibold">
                                <td class="py-2 pr-3" colspan="2">Total</td>
                                <td class="py-2 pr-3 text-right">{{ number_format($sum['register']) }}</td>
                                <td class="py-2 pr-3 text-right">{{ number_format($sum['scan']) }}</td>
                                <td class="py-2 pr-3 text-right">{{ number_format($sum['belum']) }}</td>
                                <td class="py-2 pr-3 text-right">{{ number_format($sum['proses']) }}</td>
                                <td class="py-2 pr-3 text-right">{{ number_format($sum['total']) }}</td>
                                <td class="py-2 pr-3 text-right">{{ number_format($sum['berchip']) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </x-card>
        @endif
    </div>
</x-layouts::app>
