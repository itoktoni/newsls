@php($logoUrl = \App\Models\WebsiteSetting::fileUrl(config('website.logo')))
<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel">
<head>
    <meta charset="UTF-8">
</head>
<body>
<table>
    <tr>
        <td colspan="4"><b>{{ $title }}</b><br><b>RUMAH SAKIT : {{ $rs->rs_nama ?? 'Semua Rumah Sakit' }}</b><br><b>Periode : {{ formatDate($start) ?? '-' }} - {{ formatDate($end) ?? '-' }}</b></td>
        <td colspan="2" style="text-align:right;">
            @if($logoUrl)
            <img src="{{ url($logoUrl) }}" alt="Logo" height="60" width="90">
            @endif
        </td>
    </tr>
</table>
<br>
<table border="1">
    <thead>
        <tr>
            <th>No.</th>
            <th>NO. DO</th>
            <th>RUMAH SAKIT</th>
            <th>TOTAL</th>
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
            <td>{{ $table['tanggal'] }}</td>
            <td>{{ $table['operator'] }}</td>
        </tr>
        @empty
        <tr><td colspan="6">Tidak ada data.</td></tr>
        @endforelse
    </tbody>
    <tfoot>
        <tr>
            <th colspan="3">Total:</th>
            <th>{{ array_sum(array_column($data, 'total')) }}</th>
            <th colspan="2"></th>
        </tr>
    </tfoot>
</table>
</body>
</html>
