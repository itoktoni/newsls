<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>PELUNASAN PENDING - {{ $rs->rs_nama ?? 'Semua Rumah Sakit' }}</title>
    <style>
        body { font-family: Arial, Helvetica, sans-serif; font-size: 11px; color: #000; margin: 20px; }
        .header { width: 100%; border: 0; margin-bottom: 10px; }
        .header h3 { margin: 2px 0; white-space: nowrap; }
        .header td { white-space: nowrap; }
        table.data { width: 100%; border-collapse: collapse; border-spacing: 0; margin-bottom: 16px; }
        table.data th, table.data td { border: 1px solid #000; padding: 4px 6px; white-space: nowrap; }
        table.data thead th { background: #eee; text-align: left; }
        table.data td.num { text-align: right; }
        table.data tfoot td { font-weight: bold; background: #eee; }
        h4 { margin: 12px 0 6px; }
        .lunas { color: #16a34a; font-weight: bold; }
        .belum { color: #dc2626; font-weight: bold; }
        .footer { width: 100%; margin-top: 20px; border: 0; }
        .print-date, .print-person { text-align: right; }
        .no-print { margin-bottom: 12px; }
        @media print { .no-print { display: none !important; } }
    </style>
</head>
<body>

<div class="no-print" style="position:fixed;top:16px;right:16px;z-index:9999;display:flex;gap:8px;">
    <button onclick="window.print()" style="padding:8px 20px;font-size:14px;cursor:pointer;box-shadow:0 2px 8px rgba(0,0,0,.2);">Cetak / Simpan PDF</button>
    <a href="{{ route('report-pelunasan-pending.getExportExcel', request()->query()) }}" style="padding:8px 20px;font-size:14px;cursor:pointer;box-shadow:0 2px 8px rgba(0,0,0,.2);background:#16a34a;color:#fff;text-decoration:none;font-family:Arial,Helvetica,sans-serif;">Export Excel</a>
</div>

@php($logoUrl = \App\Models\WebsiteSetting::fileUrl(config('website.logo')))
<table class="header">
    <tr>
        <td style="vertical-align:middle;">
            <h3><b>PELUNASAN PENDING</b></h3>
            <h3>RUMAH SAKIT : {{ $rs->rs_nama ?? 'Semua Rumah Sakit' }}</h3>
            <h3>Periode Kotor : {{ formatDate($start) ?? '-' }} - {{ formatDate($end) ?? '-' }}</h3>
            <h3>Total Sisa Hutang : {{ $totalSisa }} pcs</h3>
        </td>
        <td style="width:100px;min-width:100px;text-align:right;vertical-align:middle;">
            @if($logoUrl)
            <img src="{{ $logoUrl }}" alt="Logo" style="display:block;margin-left:auto;width:90px;max-width:90px;height:auto;max-height:60px;object-fit:contain;">
            @endif
        </td>
    </tr>
</table>

<br>

<h4>RINCIAN HUTANG (per tanggal kotor)</h4>
<div class="table-responsive" id="table_data">
    <table class="data">
        <thead>
            <tr>
                <th width="1">No.</th>
                <th>TGL KOTOR</th>
                <th>RUMAH SAKIT</th>
                <th>JENIS LINEN</th>
                <th>STATUS</th>
                <th>SISA AWAL</th>
                <th>TGL BAYAR</th>
                <th>KODE BAYAR</th>
                <th>BAYAR</th>
                <th>SISA</th>
                <th>STATUS</th>
            </tr>
        </thead>
        <tbody>
            @forelse($lots as $lot)
            @forelse($lot['cicilan'] as $c)
            <tr>
                <td>{{ $loop->parent->iteration }}.{{ $loop->iteration }}</td>
                <td>{{ formatDate($lot['tanggal']) ?? $lot['tanggal'] }}</td>
                <td>{{ $lot['rs_nama'] }}</td>
                <td>{{ $lot['jenis_nama'] }}</td>
                <td>{{ $lot['status'] }}</td>
                <td class="num">{{ $c['sisa_sebelum'] }}</td>
                <td>{{ formatDate($c['tanggal']) ?? $c['tanggal'] }}</td>
                <td>{{ $c['code'] }}</td>
                <td class="num">{{ $c['qty'] }}</td>
                <td class="num">{{ $c['sisa_setelah'] }}</td>
                <td class="{{ $c['lunas_setelah'] ? 'lunas' : 'belum' }}">{{ $c['lunas_setelah'] ? 'LUNAS' : 'BELUM' }}</td>
            </tr>
            @empty
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ formatDate($lot['tanggal']) ?? $lot['tanggal'] }}</td>
                <td>{{ $lot['rs_nama'] }}</td>
                <td>{{ $lot['jenis_nama'] }}</td>
                <td>{{ $lot['status'] }}</td>
                <td class="num">{{ $lot['jumlah'] }}</td>
                <td>-</td>
                <td>-</td>
                <td class="num">0</td>
                <td class="num">{{ $lot['sisa'] }}</td>
                <td class="belum">BELUM</td>
            </tr>
            @endforelse
            @empty
            <tr><td colspan="11" style="text-align:center;">Tidak ada data.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<h4>RINCIAN PEMBAYARAN (per delivery)</h4>
<div class="table-responsive" id="table_bayar">
    <table class="data">
        <thead>
            <tr>
                <th width="1">No.</th>
                <th>KODE DELIVERY</th>
                <th>TGL KIRIM</th>
                <th>RUMAH SAKIT</th>
                <th>JENIS LINEN</th>
                <th>JUMLAH</th>
                <th>ALOKASI KE LOT</th>
                <th>KELEBIHAN</th>
            </tr>
        </thead>
        <tbody>
            @forelse($payments as $pay)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $pay['code'] }}</td>
                <td>{{ formatDate($pay['tanggal']) ?? $pay['tanggal'] }}</td>
                <td>{{ $pay['rs_nama'] }}</td>
                <td>{{ $pay['jenis_nama'] }}</td>
                <td class="num">{{ $pay['jumlah'] }}</td>
                <td>
                    @forelse($pay['alokasi'] as $a)
                        {{ $a['qty'] }} untuk {{ formatDate($a['lot_tanggal']) ?? $a['lot_tanggal'] }}@if(!$loop->last); @endif
                    @empty
                        -
                    @endforelse
                </td>
                <td class="num">{{ $pay['kelebihan'] }}</td>
            </tr>
            @empty
            <tr><td colspan="8" style="text-align:center;">Tidak ada data.</td></tr>
            @endforelse
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
