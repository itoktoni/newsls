@php($logoUrl = \App\Models\WebsiteSetting::fileUrl(config('website.logo')))
<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel">
<head><meta charset="UTF-8"></head>
<body>
<table>
    <tr>
        <td colspan="{{ count($dates) * 3 + 3 }}"><b>IN VS OUT</b><br><b>RUMAH SAKIT : {{ $rs->rs_nama ?? 'Semua Rumah Sakit' }}</b><br><b>Periode : {{ formatDate($start) ?? '-' }} - {{ formatDate($end) ?? '-' }}</b><br><b><i>In = transaksi_grouping_date; Out T = bersih_report T+1; Selisih = In - Out</i></b></td>
        <td colspan="2" style="text-align:right;">
            @if($logoUrl)<img src="{{ url($logoUrl) }}" alt="Logo" height="60" width="90">@endif
        </td>
    </tr>
</table>
<br>
<table border="1">
    <thead>
        <tr>
            <th>No.</th><th>Nama Linen</th>
            @foreach($dates as $tgl)
            <th>{{ \Carbon\Carbon::parse($tgl)->format('d/m') }} In</th>
            <th>{{ \Carbon\Carbon::parse($tgl)->format('d/m') }} Out</th>
            <th>{{ \Carbon\Carbon::parse($tgl)->format('d/m') }} Selisih</th>
            @endforeach
            <th>Total In</th><th>Total Out</th><th>Total Selisih</th>
        </tr>
    </thead>
    <tbody>
        @forelse($linens as $jenisKey => $nama)
        <tr>
            <td>{{ $loop->iteration }}</td><td>{{ strtoupper($nama) }}</td>
            @foreach($dates as $tgl)
            <td>{{ $in['qty'][$tgl][$jenisKey] ?? 0 }}</td>
            <td>{{ $out['qty'][$tgl][$jenisKey] ?? 0 }}</td>
            <td>{{ ($in['qty'][$tgl][$jenisKey] ?? 0) - ($out['qty'][$tgl][$jenisKey] ?? 0) }}</td>
            @endforeach
            <td>{{ $rowIn[$jenisKey] ?? 0 }}</td><td>{{ $rowOut[$jenisKey] ?? 0 }}</td><td>{{ $rowSelisih[$jenisKey] ?? 0 }}</td>
        </tr>
        @empty
        <tr><td colspan="{{ count($dates) * 3 + 5 }}">Tidak ada data.</td></tr>
        @endforelse
    </tbody>
    <tfoot>
        <tr>
            <th colspan="2">Total:</th>
            @foreach($dates as $tgl)
            <th>{{ $colIn[$tgl] ?? 0 }}</th><th>{{ $colOut[$tgl] ?? 0 }}</th><th>{{ $colSelisih[$tgl] ?? 0 }}</th>
            @endforeach
            <th>{{ $grandIn }}</th><th>{{ $grandOut }}</th><th>{{ $grandSelisih }}</th>
        </tr>
    </tfoot>
</table>
</body>
</html>
