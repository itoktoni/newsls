<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>IN VS OUT - {{ $rs->rs_nama ?? '' }}</title>
    <style>
        body { font-family: Arial, Helvetica, sans-serif; font-size: 11px; color: #000; margin: 20px; }
        .header, .footer { width: 100%; border: 0; }
        .header { margin-bottom: 10px; }
        .header h3 { margin: 2px 0; white-space: nowrap; }
        .header td { white-space: nowrap; }
        table.data { width: 100%; border-collapse: collapse; border-spacing: 0; }
        table.data th, table.data td { border: 1px solid #000; padding: 4px 6px; white-space: nowrap; }
        table.data thead th, table.data tfoot th { background: #eee; text-align: left; }
        table.data .num { text-align: right; }
        table.data .i { background: #dbeafe; }
        table.data .o { background: #dcfce7; }
        table.data .s { background: #fee2e2; }
        .footer { margin-top: 20px; }
        .print-date, .print-person { text-align: right; }
        .no-print { margin-bottom: 12px; }
        @media print { .no-print { display: none !important; } }
    </style>
</head>
<body>
<div class="no-print" style="position:fixed;top:16px;right:16px;z-index:9999;display:flex;gap:8px;">
    <button onclick="window.print()" style="padding:8px 20px;font-size:14px;cursor:pointer;box-shadow:0 2px 8px rgba(0,0,0,.2);">Cetak / Simpan PDF</button>
    <a href="{{ route('report-in-vs-out.getExportExcel', request()->query()) }}" style="padding:8px 20px;font-size:14px;cursor:pointer;box-shadow:0 2px 8px rgba(0,0,0,.2);background:#16a34a;color:#fff;text-decoration:none;font-family:Arial,Helvetica,sans-serif;">Export Excel</a>
</div>

@php($logoUrl = \App\Models\WebsiteSetting::fileUrl(config('website.logo')))
<table class="header">
    <tr>
        <td style="vertical-align:middle;">
            <h3><b>IN VS OUT</b></h3>
            <h3>RUMAH SAKIT : {{ $rs->rs_nama ?? 'Semua Rumah Sakit' }}</h3>
            <h3>Periode : {{ formatDate($start) ?? '-' }} - {{ formatDate($end) ?? '-' }}</h3>
            <h3><i>In = transaksi_grouping_date; Out T = bersih_report T+1; Selisih = In - Out</i></h3>
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
                <th rowspan="2">No.</th>
                <th rowspan="2" style="width:200px;">Nama Linen</th>
                @foreach($dates as $tgl)
                <th colspan="3" style="text-align:center;">{{ \Carbon\Carbon::parse($tgl)->format('d') }}</th>
                @endforeach
                <th rowspan="2" class="num">Total In</th>
                <th rowspan="2" class="num">Total Out</th>
                <th rowspan="2" class="num">Total Selisih</th>
            </tr>
            <tr>
                @foreach($dates as $tgl)
                <th class="i num">I</th>
                <th class="o num">O</th>
                <th class="s num">S</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @forelse($linens as $jenisKey => $nama)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ strtoupper($nama) }}</td>
                @foreach($dates as $tgl)
                <td class="num">{{ $in['qty'][$tgl][$jenisKey] ?? 0 }}</td>
                <td class="num">{{ $out['qty'][$tgl][$jenisKey] ?? 0 }}</td>
                <td class="num">{{ ($in['qty'][$tgl][$jenisKey] ?? 0) - ($out['qty'][$tgl][$jenisKey] ?? 0) }}</td>
                @endforeach
                <td class="num"><b>{{ $rowIn[$jenisKey] ?? 0 }}</b></td>
                <td class="num"><b>{{ $rowOut[$jenisKey] ?? 0 }}</b></td>
                <td class="num"><b>{{ $rowSelisih[$jenisKey] ?? 0 }}</b></td>
            </tr>
            @empty
            <tr><td colspan="{{ count($dates) * 3 + 5 }}" style="text-align:center;">Tidak ada data.</td></tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <th colspan="2" style="text-align:right;">Total:</th>
                @foreach($dates as $tgl)
                <th class="num">{{ $colIn[$tgl] ?? 0 }}</th>
                <th class="num">{{ $colOut[$tgl] ?? 0 }}</th>
                <th class="num">{{ $colSelisih[$tgl] ?? 0 }}</th>
                @endforeach
                <th class="num">{{ $grandIn }}</th>
                <th class="num">{{ $grandOut }}</th>
                <th class="num">{{ $grandSelisih }}</th>
            </tr>
        </tfoot>
    </table>
</div>

<table class="footer">
    <tr><td colspan="2" class="print-date">{{ config('app.location') }}, {{ date('d F Y') }}</td></tr>
    <tr><td colspan="2" class="print-person">{{ auth()->user()->name ?? '' }}</td></tr>
</table>
</body>
</html>
