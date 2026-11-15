@php($logoUrl = \App\Models\WebsiteSetting::fileUrl(config('website.logo')))
<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel">
<head>
    <meta charset="UTF-8">
</head>
<body>
<table>
    <tr>
        <td colspan="8"><b>{{ $title }}</b><br><b>RUMAH SAKIT : {{ $rs->rs_nama ?? 'Semua Rumah Sakit' }}</b><br><b>Status : {{ $status ?? 'Semua' }} | Periode : {{ formatDate($start) ?? '-' }} - {{ formatDate($end) ?? '-' }}</b></td>
        <td colspan="2" style="text-align:right;">
            @if($logoUrl)
            <img src="{{ url($logoUrl) }}" alt="Logo" height="60" width="90">
            @endif
        </td>
    </tr>
</table>
<br>
@php($showBerat = config('laundry.berat_kalkulasi', true))
<table border="1">
    <tr><th colspan="{{ $showBerat ? 4 : 1 }}">RINGKASAN ({{ $total['n_transaksi'] }} transaksi, ditimbang: {{ $total['n_timbang'] }})</th></tr>
    <tr><th>Kotor Timbangan (Kg)</th>@if($showBerat)<th>Bersih Hitung (Kg)</th><th>Selisih (Kg)</th><th>N Transaksi</th>@endif</tr>
    <tr><td>{{ $total['kotor'] ?? 'belum ditimbang' }}</td>@if($showBerat)<td>{{ $total['bersih'] }}</td><td>{{ $total['selisih'] ?? '-' }}</td><td>{{ $total['n_transaksi'] }}</td>@endif</tr>
</table>
<br>
<table border="1">
    <thead>
        <tr>
            <th>No.</th>
            <th>Kode Transaksi</th>
            <th>Tanggal</th>
            <th>Rumah Sakit</th>
            <th>Operator</th>
            <th>Status</th>
            <th>N RFID</th>
            <th>Kotor (Kg)</th>
            @if($showBerat)
            <th>Bersih (Kg)</th>
            <th>Selisih (Kg)</th>
            @endif
        </tr>
    </thead>
    <tbody>
        @forelse($data as $row)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $row->master_kode }}</td>
            <td>{{ $row->master_tanggal }}</td>
            <td>{{ $row->rs_nama ?? '-' }}</td>
            <td>{{ $row->operator_nama ?? '-' }}</td>
            <td>{{ $row->master_status }}</td>
            <td>{{ $row->n_rfid }}</td>
            <td>{{ $row->master_qty ?? 'belum ditimbang' }}</td>
            @if($showBerat)
            <td>{{ $row->bersih ?? 0 }}</td>
            <td>{{ $row->selisih ?? '-' }}</td>
            @endif
        </tr>
        @empty
        <tr><td colspan="{{ $showBerat ? 10 : 8 }}">Tidak ada data.</td></tr>
        @endforelse
    </tbody>
    <tfoot>
        <tr>
            <th colspan="6">Total:</th>
            <th>{{ $data->sum('n_rfid') }}</th>
            <th>{{ $total['kotor'] ?? '-' }}</th>
            @if($showBerat)
            <th>{{ $total['bersih'] }}</th>
            <th>{{ $total['selisih'] ?? '-' }}</th>
            @endif
        </tr>
    </tfoot>
</table>
</body>
</html>
