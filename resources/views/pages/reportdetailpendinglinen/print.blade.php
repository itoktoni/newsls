<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>PENDING OUTSTANDING - {{ $rs->rs_nama ?? 'Semua Rumah Sakit' }}</title>
    <style>
        body { font-family: Arial, Helvetica, sans-serif; font-size: 11px; color: #000; margin: 20px; }
        .header { width: 100%; border: 0; margin-bottom: 10px; }
        .header h3 { margin: 2px 0; white-space: nowrap; }
        .header td { white-space: nowrap; }
        table.data { width: 100%; border-collapse: collapse; border-spacing: 0; }
        table.data th, table.data td { border: 1px solid #000; padding: 4px 6px; white-space: nowrap; }
        table.data thead th { background: #eee; text-align: left; }
        table.data td.num { text-align: right; }
        .footer { width: 100%; margin-top: 20px; border: 0; }
        .print-date, .print-person { text-align: right; }
        .no-print { margin-bottom: 12px; }
        @media print { .no-print { display: none !important; } }
    </style>
</head>
<body>

<div class="no-print" style="position:fixed;top:16px;right:16px;z-index:9999;display:flex;gap:8px;">
    <button onclick="window.print()" style="padding:8px 20px;font-size:14px;cursor:pointer;box-shadow:0 2px 8px rgba(0,0,0,.2);">Cetak / Simpan PDF</button>
    <a href="{{ route('report-detail-pending-linen.getExportExcel', request()->query()) }}" style="padding:8px 20px;font-size:14px;cursor:pointer;box-shadow:0 2px 8px rgba(0,0,0,.2);background:#16a34a;color:#fff;text-decoration:none;font-family:Arial,Helvetica,sans-serif;">Export Excel</a>
</div>

@php($logoUrl = \App\Models\WebsiteSetting::fileUrl(config('website.logo')))
<table class="header">
    <tr>
        <td style="vertical-align:middle;">
            <h3><b>PENDING OUTSTANDING</b></h3>
            <h3>RUMAH SAKIT : {{ $rs->rs_nama ?? 'Semua Rumah Sakit' }}</h3>
            <h3>Periode : {{ formatDate($start) ?? '-' }} - {{ formatDate($end) ?? '-' }}</h3>
            @if(!empty($stagnanLabel))
            <h3>{{ $stagnanLabel }}</h3>
            @endif
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
                <th>NO. RFID</th>
                <th>LINEN</th>
                <th>RUMAH SAKIT</th>
                <th>RUANGAN</th>
                <th>JUMLAH PEMAKAIAN</th>
                <th>TANGGAL KOTOR</th>
                <th>TANGGAL UPDATE</th>
                <th>LAMA PENDING</th>
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
                <td class="num">{{ $table->detail_total_bersih ?? 0 }}</td>
                <td>{{ formatDate($table->outstanding_created_at) ?? '-' }}</td>
                <td>{{ formatDate($table->outstanding_updated_at) ?? '-' }}</td>
                <td>{{ ($table->outstanding_pending_created_at ?? $table->outstanding_updated_at) ? (int) floor(\Illuminate\Support\Carbon::parse($table->outstanding_pending_created_at ?? $table->outstanding_updated_at)->diffInDays(now())).' Hari' : '0 Hari' }}</td>
                <td>{{ \App\Enums\TransactionType::getDescription($table->outstanding_status_transaksi ?? '') ?: ($table->outstanding_status_transaksi ?? '-') }}</td>
                <td>{{ $table->outstanding_status_proses ?? '-' }}</td>
            </tr>
            @empty
            <tr><td colspan="11" style="text-align:center;">Tidak ada data.</td></tr>
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
