<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>REKAP OPNAME - {{ $opname->hasRs?->rs_nama ?? '' }}</title>
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
    <a href="{{ route('report-rekap-opname.getExportExcel', request()->query()) }}" style="padding:8px 20px;font-size:14px;cursor:pointer;box-shadow:0 2px 8px rgba(0,0,0,.2);background:#16a34a;color:#fff;text-decoration:none;font-family:Arial,Helvetica,sans-serif;">Export Excel</a>
</div>

@php
    $logoUrl = \App\Models\WebsiteSetting::fileUrl(config('website.logo'));
    $rows = [];
    $colSa = $colSo = [];
    $totalMinus = $totalPlus = 0;
    foreach ($locations as $locId => $locName) {
        $colSa[$locId] = 0;
        $colSo[$locId] = 0;
    }
    foreach ($linens as $jenisId => $jenisNama) {
        $rowSa = $rowSo = 0;
        $cells = [];
        foreach ($locations as $locId => $locName) {
            $sVal = (int) ($sa[$jenisId][$locId] ?? 0);
            $oVal = (int) ($so[$jenisId][$locId] ?? 0);
            $cells[$locId] = [$sVal, $oVal];
            $rowSa += $sVal;
            $rowSo += $oVal;
            $colSa[$locId] += $sVal;
            $colSo[$locId] += $oVal;
        }
        $selisih = $rowSo - $rowSa;
        $plus = $selisih >= 0 ? $selisih : 0;
        $minus = $selisih < 0 ? $selisih : 0;
        $totalPlus += $plus;
        $totalMinus += $minus;
        $rows[] = compact('jenisNama', 'cells', 'rowSa', 'rowSo', 'minus', 'plus');
    }
    $grandSa = array_sum($colSa);
    $grandSo = array_sum($colSo);
@endphp

<table class="header">
    <tr>
        <td style="vertical-align:middle;">
            <h3><b>REKAP OPNAME</b></h3>
            <h3>RUMAH SAKIT : {{ $opname->hasRs?->rs_nama ?? 'Semua Rumah Sakit' }}</h3>
            <h3>OPNAME : #{{ $opname->opname_id }} {{ $opname->opname_nama }}</h3>
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
                <th width="1">No.</th>
                <th style="width:200px;">Nama Linen</th>
                @foreach($locations as $locId => $locName)
                <th colspan="2" style="text-align:center;">{{ $locName }}</th>
                @endforeach
                <th colspan="2" style="text-align:center;">Total</th>
                <th colspan="2" style="text-align:center;">Selisih</th>
            </tr>
            <tr>
                <th></th>
                <th></th>
                @foreach($locations as $locId => $locName)
                <th class="num">SA</th>
                <th class="num">SO</th>
                @endforeach
                <th class="num">SA</th>
                <th class="num">SO</th>
                <th class="num">-</th>
                <th class="num">+</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rows as $i => $row)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $row['jenisNama'] }}</td>
                @foreach($row['cells'] as $cell)
                <td class="num">{{ $cell[0] }}</td>
                <td class="num">{{ $cell[1] }}</td>
                @endforeach
                <td class="num"><b>{{ $row['rowSa'] }}</b></td>
                <td class="num"><b>{{ $row['rowSo'] }}</b></td>
                <td class="num">{{ $row['minus'] }}</td>
                <td class="num">{{ $row['plus'] }}</td>
            </tr>
            @empty
            <tr><td colspan="{{ 4 + (count($locations) * 2) + 4 }}" style="text-align:center;">Tidak ada data.</td></tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <th colspan="2" style="text-align:right;">Total:</th>
                @foreach($locations as $locId => $locName)
                <th class="num">{{ $colSa[$locId] ?? 0 }}</th>
                <th class="num">{{ $colSo[$locId] ?? 0 }}</th>
                @endforeach
                <th class="num">{{ $grandSa }}</th>
                <th class="num">{{ $grandSo }}</th>
                <th class="num">{{ $totalMinus }}</th>
                <th class="num">{{ $totalPlus }}</th>
            </tr>
        </tfoot>
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
