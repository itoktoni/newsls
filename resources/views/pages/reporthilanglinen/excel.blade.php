@php($logoUrl = \App\Models\WebsiteSetting::fileUrl(config('website.logo')))
<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel">
<head>
    <meta charset="UTF-8">
</head>
<body>
<table>
    <tr>
        <td colspan="6"><b>REKAP LINEN STAGNAN DI RS</b><br><b>RUMAH SAKIT : {{ $rs->rs_nama ?? 'Semua Rumah Sakit' }}</b><br><b>{{ $stagnanLabel ?? '' }}</b></td>
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
            <th>NO. RFID</th>
            <th>LINEN</th>
            <th>RUMAH SAKIT</th>
            <th>RUANGAN</th>
            <th>JUMLAH PEMAKAIAN</th>
            <th>UPDATE TERAKHIR</th>
            <th>LAMA DIAM</th>
        </tr>
    </thead>
    <tbody>
        @forelse($data as $table)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $table->detail_rfid }}</td>
            <td>{{ $table->jenis_nama ?? '-' }}</td>
            <td>{{ $table->rs_nama ?? '-' }}</td>
            <td>{{ $table->ruangan_nama ?? '-' }}</td>
            <td>{{ $table->detail_total_bersih ?? 0 }}</td>
            <td>{{ formatDate($table->detail_updated_at) ?? '-' }}</td>
            <td>{{ $table->detail_updated_at ? (int) floor(\Illuminate\Support\Carbon::parse($table->detail_updated_at)->diffInDays(now())).' Hari' : '0 Hari' }}</td>
        </tr>
        @empty
        <tr><td colspan="8">Tidak ada data.</td></tr>
        @endforelse
    </tbody>
</table>
</body>
</html>
