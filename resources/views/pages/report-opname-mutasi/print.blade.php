<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>REPORT OPNAME MUTASI - {{ $opname->hasRs?->rs_nama ?? '' }}</title>
    <style>
        body { font-family: Arial, Helvetica, sans-serif; font-size: 11px; color: #000; margin: 20px; }
        .header { width: 100%; border: 0; margin-bottom: 10px; }
        .header h3 { margin: 2px 0; white-space: nowrap; }
        .header td { white-space: nowrap; }
        table.data { width: 100%; border-collapse: collapse; border-spacing: 0; }
        table.data th, table.data td { border: 1px solid #000; padding: 4px 6px; white-space: nowrap; }
        table.data thead th { background: #eee; text-align: center; vertical-align: middle; }
        table.data td.num { text-align: right; }
        table.data tr.total td { font-weight: bold; background: #f3f4f6; }
        .footer { width: 100%; margin-top: 20px; border: 0; }
        .print-date, .print-person { text-align: right; }
        .no-print { margin-bottom: 12px; }
        @media print { .no-print { display: none !important; } }
    </style>
</head>
<body>

<div class="no-print" style="position:fixed;top:16px;right:16px;z-index:9999;display:flex;gap:8px;">
    <button onclick="window.print()" style="padding:8px 20px;font-size:14px;cursor:pointer;box-shadow:0 2px 8px rgba(0,0,0,.2);">Cetak / Simpan PDF</button>
    <a href="{{ route('report-opname-mutasi.getExportExcel', request()->query()) }}" style="padding:8px 20px;font-size:14px;cursor:pointer;box-shadow:0 2px 8px rgba(0,0,0,.2);background:#16a34a;color:#fff;text-decoration:none;font-family:Arial,Helvetica,sans-serif;">Export Excel</a>
</div>

@php
    $logoUrl = \App\Models\WebsiteSetting::fileUrl(config('website.logo'));
@endphp
<table class="header">
    <tr>
        <td style="vertical-align:middle;">
            <h3><b>REPORT OPNAME MUTASI</b></h3>
            <h3>OPNAME ID : {{ $opname->opname_id }}</h3>
            <h3>RUMAH SAKIT : {{ $opname->hasRs?->rs_nama ?? 'Semua Rumah Sakit' }}</h3>
            <h3>Periode : {{ formatDate($opname->opname_mulai) ?? '-' }} - {{ formatDate($opname->opname_selesai) ?? '-' }}</h3>
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
                <th rowspan="3" width="1">No.</th>
                <th rowspan="3">TANGGAL</th>
                <th rowspan="3">REGISTER<br>SAAT SO</th>
                <th colspan="2">SCAN LINEN</th>
                <th rowspan="3">LINEN MASIH<br>DALAM PROSES</th>
                <th rowspan="3">TOTAL OPNAME</th>
                <th rowspan="3">LINEN BERCHIP<br>YANG TIDAK<br>TERINDENTIFIKASI</th>
            </tr>
            <tr>
                <th rowspan="2">TERBACA<br>SAAT SO DI RS</th>
                <th>BELUM</th>
            </tr>
            <tr>
                <th>TERBACA<br>DI LAUNDRY</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rows as $row)
            <tr>
                <td>{{ $row['no'] }}</td>
                <td>{{ $row['tanggal'] }}</td>
                <td class="num">{{ number_format($row['register']) }}</td>
                <td class="num">{{ number_format($row['scan']) }}</td>
                <td class="num">{{ number_format($row['belum']) }}</td>
                <td class="num">{{ number_format($row['proses']) }}</td>
                <td class="num">{{ number_format($row['total']) }}</td>
                <td class="num">{{ number_format($row['berchip']) }}</td>
            </tr>
            @empty
            <tr><td colspan="8" style="text-align:center;">Tidak ada data.</td></tr>
            @endforelse
            @if (! empty($rows))
            <tr class="total">
                <td colspan="2">Total</td>
                <td class="num">{{ number_format($sum['register']) }}</td>
                <td class="num">{{ number_format($sum['scan']) }}</td>
                <td class="num">{{ number_format($sum['belum']) }}</td>
                <td class="num">{{ number_format($sum['proses']) }}</td>
                <td class="num">{{ number_format($sum['total']) }}</td>
                <td class="num">{{ number_format($sum['berchip']) }}</td>
            </tr>
            @endif
        </tbody>
    </table>
</div>

<table class="footer">
    <tr>
        <td colspan="2" class="print-date">{{ config('app.location') }}, {{ date('d F Y') }}</td>
    </tr>
    <tr>
        <td colspan="2" class="print-person">{{ auth()->user()->name ?? '' }}</td>
    </tr>
</table>

</body>
</html>
