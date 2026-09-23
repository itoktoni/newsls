<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>REKAP KOTOR - {{ $rs->rs_nama ?? '' }}</title>
    <style>
        body { font-family: Arial, Helvetica, sans-serif; font-size: 11px; color: #000; margin: 20px; }
        .header { width: 100%; border: 0; margin-bottom: 10px; }
        .header h3 { margin: 2px 0; white-space: nowrap; }
        .header td { white-space: nowrap; }
        table.data { width: 100%; border-collapse: collapse; border-spacing: 0; }
        table.data th, table.data td { border: 1px solid #000; padding: 4px 6px; white-space: nowrap; }
        table.data thead th { background: #eee; text-align: left; }
        table.data td.num, table.data th.num { text-align: right; }
        table.data tfoot th { background: #eee; }
        .footer { width: 100%; margin-top: 20px; border: 0; }
        .print-date, .print-person { text-align: right; }
        .no-print { margin-bottom: 12px; }
        @media print { .no-print { display: none !important; } }
    </style>
</head>
<body>

<div class="no-print" style="position:fixed;top:16px;right:16px;z-index:9999;display:flex;gap:8px;">
    <button onclick="window.print()" style="padding:8px 20px;font-size:14px;cursor:pointer;box-shadow:0 2px 8px rgba(0,0,0,.2);">Cetak / Simpan PDF</button>
    <a href="{{ route('report-rekap-kotor.getExportExcel', request()->query()) }}" style="padding:8px 20px;font-size:14px;cursor:pointer;box-shadow:0 2px 8px rgba(0,0,0,.2);background:#16a34a;color:#fff;text-decoration:none;font-family:Arial,Helvetica,sans-serif;">Export Excel</a>
</div>

@php($logoUrl = \App\Models\WebsiteSetting::fileUrl(config('website.logo')))
<table class="header">
    <tr>
        <td style="vertical-align:middle;">
            <h3><b>REKAP KOTOR</b></h3>
            <h3>RUMAH SAKIT : {{ $rs->rs_nama ?? 'Semua Rumah Sakit' }}</h3>
            <h3>Tanggal Kotor : {{ formatDate($start) ?? '-' }} - {{ formatDate($end) ?? '-' }}</h3>
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
                <th style="width:200px;">Nama Linen</th>
                @foreach($locations as $locName)
                <th>{{ $locName }}</th>
                @endforeach
                <th class="num">Total Kotor (Pcs)</th>
                <th class="num">(Kg) Kotor</th>
            </tr>
        </thead>
        <tbody>
            @forelse($linens as $jenisKey => $linen)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $linen['nama'] }}</td>
                @foreach($locations as $locKey => $locName)
                <td class="num">{{ $matrix[$jenisKey][$locKey] ?? 0 }}</td>
                @endforeach
                <td class="num"><b>{{ $rowTotal[$jenisKey] ?? 0 }}</b></td>
                <td class="num"><b>{{ formatQty($rowKg[$jenisKey] ?? 0) }}</b></td>
            </tr>
            @empty
            <tr><td colspan="{{ count($locations) + 4 }}" style="text-align:center;">Tidak ada data.</td></tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <th colspan="2" style="text-align:right;">Total:</th>
                @foreach($locations as $locKey => $locName)
                <th class="num">{{ $colTotal[$locKey] ?? 0 }}</th>
                @endforeach
                <th class="num">{{ $grandQty }}</th>
                <th class="num">{{ formatQty($grandKg) }}</th>
            </tr>
        </tfoot>
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
