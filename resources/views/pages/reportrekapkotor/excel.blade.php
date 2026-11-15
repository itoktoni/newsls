@php($logoUrl = \App\Models\WebsiteSetting::fileUrl(config('website.logo')))
<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel">
<head>
    <meta charset="UTF-8">
</head>
<body>
<table>
    <tr>
        <td colspan="{{ count($locations) + 2 }}"><b>REKAP KOTOR</b><br><b>RUMAH SAKIT : {{ $rs->rs_nama ?? 'Semua Rumah Sakit' }}</b><br><b>Tanggal Kotor : {{ formatDate($start) ?? '-' }} - {{ formatDate($end) ?? '-' }}</b></td>
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
    <tr><th colspan="{{ $showBerat ? 4 : 1 }}">RINGKASAN BERAT TIMBANGAN ({{ $berat['n_transaksi'] ?? 0 }} transaksi, ditimbang: {{ $berat['n_timbang'] ?? 0 }})</th></tr>
    <tr><th>Kotor Timbangan (Kg)</th>@if($showBerat)<th>Bersih Hitung (Kg)</th><th>Selisih (Kg)</th><th>N Transaksi</th>@endif</tr>
    <tr><td>{{ ($berat['kotor'] ?? null) !== null ? $berat['kotor'] : 'belum ditimbang' }}</td>@if($showBerat)<td>{{ $berat['bersih'] ?? 0 }}</td><td>{{ ($berat['selisih'] ?? null) !== null ? $berat['selisih'] : '-' }}</td><td>{{ $berat['n_transaksi'] ?? 0 }}</td>@endif</tr>
</table>
<br>
<table border="1">
    <thead>
        <tr>
            <th>No.</th>
            <th>Nama Linen</th>
            @foreach($locations as $locName)
            <th>{{ $locName }}</th>
            @endforeach
            <th>Total Kotor (Pcs)</th>
            @if($showBerat)
            <th>(Kg) Kotor</th>
            @endif
        </tr>
    </thead>
    <tbody>
        @forelse($linens as $jenisKey => $linen)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $linen['nama'] }}</td>
            @foreach($locations as $locKey => $locName)
            <td>{{ $matrix[$jenisKey][$locKey] ?? 0 }}</td>
            @endforeach
            <td>{{ $rowTotal[$jenisKey] ?? 0 }}</td>
            @if($showBerat)
            <td>{{ $rowKg[$jenisKey] ?? 0 }}</td>
            @endif
        </tr>
        @empty
        <tr><td colspan="{{ count($locations) + ($showBerat ? 4 : 3) }}">Tidak ada data.</td></tr>
        @endforelse
    </tbody>
    <tfoot>
        <tr>
            <th colspan="2">Total:</th>
            @foreach($locations as $locKey => $locName)
            <th>{{ $colTotal[$locKey] ?? 0 }}</th>
            @endforeach
            <th>{{ $grandQty }}</th>
            @if($showBerat)
            <th>{{ $grandKg }}</th>
            @endif
        </tr>
    </tfoot>
</table>
</body>
</html>
