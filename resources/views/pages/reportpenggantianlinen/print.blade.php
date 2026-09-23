<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>DETAIL PENGGANTIAN TAG LINEN - {{ $rs->rs_nama ?? 'Semua Rumah Sakit' }}</title>
    <style>
        body { font-family: Arial, Helvetica, sans-serif; font-size: 11px; color: #000; margin: 20px; }
        .header { width: 100%; border: 0; margin-bottom: 10px; }
        .header h3 { margin: 2px 0; white-space: nowrap; }
        .header td { white-space: nowrap; }
        table.data { width: 100%; border-collapse: collapse; border-spacing: 0; }
        table.data th, table.data td { border: 1px solid #000; padding: 4px 6px; white-space: nowrap; }
        table.data thead th { background: #eee; text-align: left; }
        .footer { width: 100%; margin-top: 20px; border: 0; }
        .print-date, .print-person { text-align: right; }
        .no-print { margin-bottom: 12px; }
        @media print { .no-print { display: none !important; } }
    </style>
</head>
<body>

<div class="no-print" style="position:fixed;top:16px;right:16px;z-index:9999;display:flex;gap:8px;">
    <button onclick="window.print()" style="padding:8px 20px;font-size:14px;cursor:pointer;box-shadow:0 2px 8px rgba(0,0,0,.2);">Cetak / Simpan PDF</button>
    <a href="{{ route('report-penggantian-linen.getExportExcel', request()->query()) }}" style="padding:8px 20px;font-size:14px;cursor:pointer;box-shadow:0 2px 8px rgba(0,0,0,.2);background:#16a34a;color:#fff;text-decoration:none;font-family:Arial,Helvetica,sans-serif;">Export Excel</a>
</div>

@php($logoUrl = \App\Models\WebsiteSetting::fileUrl(config('website.logo')))
<table class="header">
    <tr>
        <td style="vertical-align:middle;">
            <h3><b>DETAIL PENGGANTIAN TAG LINEN</b></h3>
            <h3>RUMAH SAKIT : {{ $rs->rs_nama ?? 'Semua Rumah Sakit' }}</h3>
            <h3>Periode : {{ formatDate($start) ?? '-' }} - {{ formatDate($end) ?? '-' }}</h3>
        </td>
        <td style="width:100px;min-width:100px;text-align:right;vertical-align:middle;">
            @if($logoUrl)
            <img src="{{ $logoUrl }}" alt="Logo" style="display:block;margin-left:auto;width:90px;max-width:90px;height:auto;max-height:60px;object-fit:contain;">
            @endif
        </td>
    </tr>
</table>

<br>

<div class="table-responsive" id="table_data">
    <table class="data">
        <thead>
            <tr>
                <th width="1">No.</th>
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
            <tr><td colspan="10" style="text-align:center;">Tidak ada data.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<table class="footer">
    <tr>
        <td colspan="2" class="print-date">{{ env('APP_LOCATION', config('app.name')) }}, {{ date('d F Y') }}</td>
    </tr>
    <tr>
        <td colspan="2" class="print-person">{{ auth()->user()->name ?? '' }}</td>
    </tr>
</table>

</body>
</html>
