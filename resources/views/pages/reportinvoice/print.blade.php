<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>REPORT INVOICE - {{ $rs->rs_nama ?? '' }}</title>
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

<div class="no-print" style="position:fixed;bottom:16px;right:16px;z-index:9999;display:flex;gap:8px;">
    <button onclick="window.print()" style="padding:8px 20px;font-size:14px;cursor:pointer;box-shadow:0 2px 8px rgba(0,0,0,.2);">Cetak / Simpan PDF</button>
    <a href="{{ route('report-invoice.getExportExcel', request()->query()) }}" style="padding:8px 20px;font-size:14px;cursor:pointer;box-shadow:0 2px 8px rgba(0,0,0,.2);background:#16a34a;color:#fff;text-decoration:none;font-family:Arial,Helvetica,sans-serif;">Export Excel</a>
</div>

@php($logoUrl = \App\Models\WebsiteSetting::fileUrl(config('website.logo')))
<table class="header">
    <tr>
        <td style="vertical-align:middle;">
            <h3><b>REPORT INVOICE</b></h3>
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

<table class="data" style="width:auto;margin-bottom:12px;">
    <thead>
        <tr><th>Ringkasan Berat</th><th class="num">Kg</th><th class="num">Rupiah</th></tr>
    </thead>
    <tbody>
        <tr><td>Timbangan (SUM transaksi_master.master_qty{{ $nTimbang ? ' — '.$nTimbang.' batch ditimbang' : '' }})</td><td class="num">{{ $timbangan !== null ? formatQty($timbangan) : 'belum ditimbang' }}</td><td class="num">{{ $rpTimbangan !== null ? formatAngka((int) round($rpTimbangan)) : '-' }}</td></tr>
        <tr><td>Estimasi (Σ qty transaksi × jenis_linen.jenis_berat)</td><td class="num">{{ formatQty($estimasiKg ?? $sumKg) }}</td><td class="num">{{ formatAngka((int) round($rpEstimasi ?? $sumRp)) }}</td></tr>
        <tr><td>Selisih (Timbangan − Estimasi)</td><td class="num">{{ $selisihKg !== null ? formatQty($selisihKg) : '-' }}</td><td class="num">-</td></tr>
    </tbody>
</table>

<div class="table-responsive" id="table_data">
    <table class="data">
        <thead>
            <tr>
                <th width="1">No.</th>
                <th style="width:250px;">Nama Linen</th>
                @foreach($dates as $tgl)
                <th class="num">{{ \Carbon\Carbon::parse($tgl)->format('d') }}</th>
                @endforeach
                <th>Type</th>
                <th class="num">Harga</th>
                <th class="num">Berat (KG)</th>
                <th class="num">QTY</th>
                <th class="num">Total Estimasi (Kg)</th>
                <th class="num">Total (Rupiah)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($linens as $jenisKey => $nama)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ strtoupper($nama) }}</td>
                @foreach($dates as $tgl)
                <td class="num">{{ $qty[$tgl][$jenisKey] ?? 0 }}</td>
                @endforeach
                <td>{{ $cuci[$jenisKey] ?? '-' }}</td>
                <td class="num">{{ formatAngka((int) $harga) }}</td>
                <td class="num">{{ formatQty($berat[$jenisKey] ?? 0) }}</td>
                <td class="num">{{ $rowQty[$jenisKey] ?? 0 }}</td>
                <td class="num">{{ formatQty($rowKg[$jenisKey] ?? 0) }}</td>
                <td class="num">{{ formatAngka((int) round($rowRp[$jenisKey] ?? 0)) }}</td>
            </tr>
            @empty
            <tr><td colspan="{{ count($dates) + 8 }}" style="text-align:center;">Tidak ada data.</td></tr>
            @endforelse
            <tr style="background:#eee;">
                <td>-</td>
                <td><b>TIMBANGAN (KG)</b></td>
                @foreach($dates as $tgl)
                <td class="num">{{ isset($timbanganHarian[$tgl]) && $timbanganHarian[$tgl] !== null ? formatQty($timbanganHarian[$tgl]) : '-' }}</td>
                @endforeach
                <td>-</td>
                <td class="num">{{ formatAngka((int) $harga) }}</td>
                <td>-</td>
                <td>-</td>
                <td class="num">{{ $timbangan !== null ? formatQty($timbangan) : 'belum ditimbang' }}</td>
                <td class="num">{{ $rpTimbangan !== null ? formatAngka((int) round($rpTimbangan)) : '-' }}</td>
            </tr>
        </tbody>
        <tfoot>
            <tr>
                <th colspan="{{ count($dates) + 5 }}" style="text-align:right;">TOTAL ESTIMASI</th>
                <th class="num">{{ $sumQty }}</th>
                <th class="num">{{ formatQty($sumKg) }}</th>
                <th class="num">{{ formatAngka((int) round($sumRp)) }}</th>
            </tr>
            <tr>
                <th colspan="{{ count($dates) + 5 }}" style="text-align:right;">TOTAL TIMBANGAN (master_qty)</th>
                <th class="num">-</th>
                <th class="num">{{ $timbangan !== null ? formatQty($timbangan) : 'belum ditimbang' }}</th>
                <th class="num">{{ $rpTimbangan !== null ? formatAngka((int) round($rpTimbangan)) : '-' }}</th>
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
