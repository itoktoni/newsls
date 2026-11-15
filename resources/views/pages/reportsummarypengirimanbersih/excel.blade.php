@php($logoUrl = \App\Models\WebsiteSetting::fileUrl(config('website.logo')))
<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel">
<head>
    <meta charset="UTF-8">
</head>
<body>
<table>
    <tr>
        <td colspan="5"><b>{{ $title }}</b><br><b>RUMAH SAKIT : {{ $rs->rs_nama ?? 'Semua Rumah Sakit' }}</b><br><b>Periode : {{ formatDate($start) ?? '-' }} - {{ formatDate($end) ?? '-' }}</b></td>
        <td colspan="2" style="text-align:right;">
            @if($logoUrl)
            <img src="{{ url($logoUrl) }}" alt="Logo" height="60" width="90">
            @endif
        </td>
    </tr>
</table>
<br>
<table border="1">
    @php($showBerat = config('laundry.berat_kalkulasi', true))
    <thead>
        <tr>
            <th>No.</th>
            <th>NO. DO</th>
            <th>RUMAH SAKIT</th>
            <th>TOTAL</th>
            @if($showBerat)
            <th>BERAT (KG)</th>
            @endif
            <th>TANGGAL DO</th>
            <th>OPERATOR</th>
        </tr>
    </thead>
    <tbody>
        @forelse($data as $table)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $table['do'] }}</td>
            <td>{{ $table['rs'] }}</td>
            <td>{{ $table['total'] }}</td>
            @if($showBerat)
            <td>{{ $table['kg'] ?? 0 }}</td>
            @endif
            <td>{{ $table['tanggal'] }}</td>
            <td>{{ $table['operator'] }}</td>
        </tr>
        @empty
        <tr><td colspan="{{ $showBerat ? 7 : 6 }}">Tidak ada data.</td></tr>
        @endforelse
    </tbody>
    <tfoot>
        @if($showBerat)
        <tr>
            <th colspan="3">Total:</th>
            <th>{{ array_sum(array_column($data, 'total')) }}</th>
            <th>{{ array_sum(array_column($data, 'kg')) }}</th>
            <th colspan="2"></th>
        </tr>
        @else
        <tr>
            <th colspan="3">Total:</th>
            <th>{{ array_sum(array_column($data, 'total')) }}</th>
            <th colspan="2"></th>
        </tr>
        @endif
    </tfoot>
</table>
</body>
</html>
