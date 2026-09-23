@php($logoUrl = \App\Models\WebsiteSetting::fileUrl(config('website.logo')))
<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel">
<head>
    <meta charset="UTF-8">
</head>
<body>
<table>
    <tr>
        <td colspan="8"><b>DETAIL PENGGANTIAN TAG LINEN</b><br><b>RUMAH SAKIT : {{ $rs->rs_nama ?? 'Semua Rumah Sakit' }}</b><br><b>Periode : {{ formatDate($start) ?? '-' }} - {{ formatDate($end) ?? '-' }}</b></td>
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
            <th>LINEN LAMA</th>
            <th>LINEN BARU</th>
            <th>JENIS LINEN</th>
            <th>RUMAH SAKIT</th>
            <th>RUANGAN</th>
            <th>CUCI/RENTAL</th>
            <th>STATUS REGISTRASI</th>
            <th>TANGGAL PENGGANTIAN</th>
            <th>OPERATOR</th>
        </tr>
    </thead>
    <tbody>
        @forelse($data as $table)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $table->ganti_rfid_lama }}</td>
            <td>{{ $table->ganti_rfid_baru }}</td>
            <td>{{ $table->jenis_nama ?? '-' }}</td>
            <td>{{ $table->rs_nama ?? '-' }}</td>
            <td>{{ $table->ruangan_nama ?? '-' }}</td>
            <td>{{ $table->detail_status_cuci ?? '-' }}</td>
            <td>{{ $table->detail_status_register ?? '-' }}</td>
            <td>{{ formatDate($table->ganti_tanggal) ?? '-' }}</td>
            <td>{{ $table->operator_nama ?? '-' }}</td>
        </tr>
        @empty
        <tr><td colspan="10">Tidak ada data.</td></tr>
        @endforelse
    </tbody>
</table>
</body>
</html>
