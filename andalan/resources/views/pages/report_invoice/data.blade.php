<table border="0" class="header">
    <tr>
        <td></td>
        <td colspan="6">
            <h3>
                <b>REPORT INVOICE </b>
            </h3>
        </td>
        <td rowspan="3">
            <x-logo />
        </td>
    </tr>
    <tr>
        <td></td>
        <td colspan="10">
            <h3>
                RUMAH SAKIT : {{ $rs->field_name ?? 'Semua Rumah Sakit' }}
            </h3>
        </td>
    </tr>
    <tr>
        <td></td>
        <td colspan="10">
            <h3>
                Periode : {{ formatDate(request()->get('start_rekap')) }} -
                {{ formatDate(request()->get('end_rekap')) }}
            </h3>
        </td>
    </tr>
</table>

<div class="table-responsive" id="table_data">
    <table id="export" border="1" style="border-collapse: collapse !important; border-spacing: 0 !important;"
        class="table table-bordered table-striped table-responsive-stack">
        <thead>
            <tr>
                <th style="width: 10px" width="1">No. </th>
                <th style="width: 250px" width="20">Nama Linen</th>
                @foreach($tanggal as $tgl)
                <th>{{ formatDate($tgl, 'd') }}</th>
                @endforeach
                <th>Type</th>
                <th>Harga</th>
                <th>Berat (KG)</th>
                <th>QTY</th>
                <th>Total (Kg)</th>
                <th>Total (Rupiah)</th>
        </thead>
        <tbody>
            @php
            $sum_kurang = $sum_lebih = $sum_per_linen = $sum_harga = $sum_berat = $sum_kg = $sum_lawan = 0;
            $total_number = $selisih = 0;
            $total_berat = 0;
            $sum_total = 0;
            @endphp

            @forelse($linen as $linen_id => $linen_nama)
            @php
            $total_number = $total_number + $loop->iteration;

            $linen_id = $linen_id;
            $nama = $linen_nama;

            $data_linen = $data->where('view_linen_id', $linen_id);
            $single = $data_linen->first();

            $qty = $data_linen->sum('view_qty');
            $kg = $data_linen->sum('view_kg');

            $total = $single->view_price * $kg;
            $sum_total = $sum_total + $total;

            @endphp
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ strtoupper($nama) }}</td>
                @foreach($tanggal as $tgl)
                <td>
                    @php
                    $total_tanggal = $data
                    ->where('view_tanggal', formatDate($tgl, 'Y-m-d'))
                    ->where('view_linen_id', $linen_id)
                    ->sum('view_qty');
                    @endphp
                    {{ $total_tanggal > 0 ? $total_tanggal : '0' }}
                </td>
                @endforeach
                <td>{{ $single->view_cuci }}</td>
                <td class="text-right">{{ $single->view_price }}</td>
                <td class="text-right">{{ $single->view_berat }}</td>
                <td class="text-right">
                    {{ $qty ?? 0 }}
                </td>
                <td class="text-right">{{ $kg }}</td>
                <td class="text-right">{{ round($total) }}</td>
            </tr>
            @empty
            @endforelse

            @php
            $sum_qty = $data->sum('view_qty');
            $sum_kg = $data->sum('view_kg');
            @endphp

            <tr>
                <td colspan="{{ $tanggal->count() + 5 }}">TOTAL DATA</td>
                <td class="text-right">{{ $sum_qty }}</td>
                <td class="text-right">{{ $sum_kg }}</td>
                <td class="text-right">{{ round($sum_total) }}</td>
            </tr>
        </tbody>

    </table>
</div>

<table class="footer">
    <tr>
        <td colspan="2" class="print-date">{{ env('APP_LOCATION') }}, {{ date('d F Y') }}</td>
    </tr>
    <tr>
        <td colspan="2" class="print-person">{{ auth()->user()->name ?? '' }}</td>
    </tr>
</table>