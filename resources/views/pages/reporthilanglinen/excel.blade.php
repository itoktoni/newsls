@php($logoUrl = \App\Models\WebsiteSetting::fileUrl(config('website.logo')))
<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel">
<head>
    <meta charset="UTF-8">
</head>
<body>
<table>
    <tr>
        <td colspan="8"><b>REKAP LINEN HILANG</b><br><b>RUMAH SAKIT : {{ $rs->rs_nama ?? 'Semua Rumah Sakit' }}</b><br><b>Periode : {{ formatDate($start) ?? '-' }} - {{ formatDate($end) ?? '-' }}</b></td>
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
            <th>TANGGAL KOTOR</th>
            <th>LAMA HILANG</th>
            <th>STATUS</th>
            <th>PROSES TERAKHIR</th>
        </tr>
    </thead>
    <tbody>
        @forelse($data as $table)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $table->outstanding_rfid }}</td>
            <td>{{ $table->jenis_nama ?? '-' }}</td>
            <td>{{ $table->rs_nama ?? '-' }}</td>
            <td>{{ $table->ruangan_nama ?? '-' }}</td>
            <td>{{ $table->detail_total_bersih ?? 0 }}</td>
            <td>{{ formatDate($table->outstanding_created_at) ?? '-' }}</td>
            <td>{{ $table->outstanding_hilang_created_at ? \Illuminate\Support\Carbon::parse($table->outstanding_hilang_created_at)->diffInDays(now()).' Hari' : '0 Hari' }}</td>
            <td>{{ \App\Enums\TransactionType::getDescription($table->outstanding_status_transaksi ?? '') ?: ($table->outstanding_status_transaksi ?? '-') }}</td>
            <td>{{ $table->outstanding_status_proses ?? '-' }}</td>
        </tr>
        @empty
        <tr><td colspan="10">Tidak ada data.</td></tr>
        @endforelse
    </tbody>
</table>
</body>
</html>
