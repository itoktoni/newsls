<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>{{ $title }} - {{ $rs->rs_nama ?? '' }}</title>
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
    <a href="{{ route('report-selisih-berat.getExportExcel', request()->query()) }}" style="padding:8px 20px;font-size:14px;cursor:pointer;box-shadow:0 2px 8px rgba(0,0,0,.2);background:#16a34a;color:#fff;text-decoration:none;font-family:Arial,Helvetica,sans-serif;">Export Excel</a>
</div>

@php($logoUrl = \App\Models\WebsiteSetting::fileUrl(config('website.logo')))
<table class="header">
    <tr>
        <td style="vertical-align:middle;">
            <h3><b>{{ $title }}</b></h3>
            <h3>RUMAH SAKIT : {{ $rs->rs_nama ?? 'Semua Rumah Sakit' }}</h3>
            <h3>Status : {{ $status ?? 'Semua' }} | Periode : {{ formatDate($start) ?? '-' }} - {{ formatDate($end) ?? '-' }}</h3>
        </td>
        <td style="width:100px;min-width:100px;text-align:right;vertical-align:middle;">
            @if($logoUrl)
            <img src="{{ $logoUrl }}" alt="Logo" style="display:block;margin-left:auto;width:90px;max-width:90px;height:auto;max-height:60px;object-fit:contain;">
            @endif
        </td>
    </tr>
</table>

<table class="data" style="margin-bottom:10px;">
    <thead>
        <tr><th colspan="4" style="text-align:left;">RINGKASAN ({{ $total['n_transaksi'] }} transaksi, ditimbang: {{ $total['n_timbang'] }})</th></tr>
        <tr><th>Kotor Timbangan (Kg)</th><th>Bersih Hitung (Kg)</th><th>Selisih (Kg)</th><th>N Transaksi</th></tr>
    </thead>
    <tbody>
        <tr>
            <td class="num">{{ $total['kotor'] !== null ? formatQty($total['kotor']) : 'belum ditimbang' }}</td>
            <td class="num">{{ formatQty($total['bersih']) }}</td>
            <td class="num">{{ $total['selisih'] !== null ? formatQty($total['selisih']) : '-' }}</td>
            <td class="num">{{ $total['n_transaksi'] }}</td>
        </tr>
    </tbody>
</table>

<br>

<div class="table-responsive" id="table_data">
    <table class="data">
        <thead>
            <tr>
                <th width="1">No.</th>
                <th>KODE TRANSAKSI</th>
                <th>TANGGAL</th>
                <th>RUMAH SAKIT</th>
                <th>OPERATOR</th>
                <th>STATUS</th>
                <th class="num">N RFID</th>
                <th class="num">KOTOR (KG)</th>
                <th class="num">BERSIH (KG)</th>
                <th class="num">SELISIH (KG)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $row)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td class="font-mono">{{ $row->master_kode }}</td>
                <td>{{ formatDate($row->master_tanggal) ?? '-' }}</td>
                <td>{{ $row->rs_nama ?? '-' }}</td>
                <td>{{ $row->operator_nama ?? '-' }}</td>
                <td>{{ $row->master_status }}</td>
                <td class="num">{{ $row->n_rfid }}</td>
                <td class="num">{{ $row->master_qty !== null ? formatQty($row->master_qty) : 'belum ditimbang' }}</td>
                <td class="num">{{ formatQty($row->bersih ?? 0) }}</td>
                <td class="num">{{ $row->selisih !== null ? formatQty($row->selisih) : '-' }}</td>
            </tr>
            @empty
            <tr><td colspan="10" style="text-align:center;">Tidak ada data.</td></tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <th colspan="6" style="text-align:right;">Total:</th>
                <th class="num">{{ $data->sum('n_rfid') }}</th>
                <th class="num">{{ $total['kotor'] !== null ? formatQty($total['kotor']) : '-' }}</th>
                <th class="num">{{ formatQty($total['bersih']) }}</th>
                <th class="num">{{ $total['selisih'] !== null ? formatQty($total['selisih']) : '-' }}</th>
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
