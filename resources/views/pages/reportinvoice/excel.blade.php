@php($logoUrl = \App\Models\WebsiteSetting::fileUrl(config('website.logo')))
<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel">
<head>
    <meta charset="UTF-8">
</head>
<body>
<table>
    <tr>
        <td colspan="{{ count($dates) + 6 }}"><b>REPORT INVOICE</b><br><b>RUMAH SAKIT : {{ $rs->rs_nama ?? 'Semua Rumah Sakit' }}</b><br><b>Periode : {{ formatDate($start) ?? '-' }} - {{ formatDate($end) ?? '-' }}</b></td>
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
            <th>Nama Linen</th>
            @foreach($dates as $tgl)
            <th>{{ \Carbon\Carbon::parse($tgl)->format('d/m') }}</th>
            @endforeach
            <th>Type</th>
            <th>Harga</th>
            <th>Berat (KG)</th>
            <th>QTY</th>
            <th>Total (Kg)</th>
            <th>Total (Rupiah)</th>
        </tr>
    </thead>
    <tbody>
        @forelse($linens as $jenisKey => $nama)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ strtoupper($nama) }}</td>
            @foreach($dates as $tgl)
            <td>{{ $qty[$tgl][$jenisKey] ?? 0 }}</td>
            @endforeach
            <td>{{ $cuci[$jenisKey] ?? '-' }}</td>
            <td>{{ $harga }}</td>
            <td>{{ $berat[$jenisKey] ?? 0 }}</td>
            <td>{{ $rowQty[$jenisKey] ?? 0 }}</td>
            <td>{{ $rowKg[$jenisKey] ?? 0 }}</td>
            <td>{{ round($rowRp[$jenisKey] ?? 0) }}</td>
        </tr>
        @empty
        <tr><td colspan="{{ count($dates) + 8 }}">Tidak ada data.</td></tr>
        @endforelse
    </tbody>
    <tfoot>
        <tr>
            <th colspan="{{ count($dates) + 5 }}">TOTAL DATA</th>
            <th>{{ $sumQty }}</th>
            <th>{{ $sumKg }}</th>
            <th>{{ round($sumRp) }}</th>
        </tr>
    </tfoot>
</table>
</body>
</html>
