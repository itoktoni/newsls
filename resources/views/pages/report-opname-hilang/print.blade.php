<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>REPORT OPNAME BELUM TERBACA - {{ $opname->hasRs?->rs_nama ?? '' }}</title>
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
    <a href="{{ route('report-opname-hilang.getExportExcel', request()->query()) }}" style="padding:8px 20px;font-size:14px;cursor:pointer;box-shadow:0 2px 8px rgba(0,0,0,.2);background:#16a34a;color:#fff;text-decoration:none;font-family:Arial,Helvetica,sans-serif;">Export Excel</a>
</div>

@php
    $logoUrl = \App\Models\WebsiteSetting::fileUrl(config('website.logo'));
@endphp
<table class="header">
    <tr>
        <td style="vertical-align:middle;">
            <h3><b>REPORT OPNAME BELUM TERBACA SAAT SO DI RS : #{{ $opname->opname_id }}</b></h3>
            <h3>RUMAH SAKIT : {{ $opname->hasRs?->rs_nama ?? 'Semua Rumah Sakit' }}</h3>
            <h3>OPNAME : {{ $opname->opname_nama }}</h3>
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
                <th>LINEN</th>
                <th>RUANGAN</th>
                <th>NO. RFID</th>
                <th>TANGGAL REGISTER</th>
                <th>STATUS TERAKHIR</th>
                <th>STATUS LINEN</th>
                <th>TANGGAL TERAKHIR</th>
                <th>LAMA HILANG (hari)</th>
                <th>OPERATOR</th>
            </tr>
        </thead>
        <tbody>
            @forelse($details->sortBy(fn ($r) => $r->hasView?->hasJenis?->jenis_nama) as $row)
            @php
                $view = $row->hasView;
            @endphp
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $view?->hasJenis?->jenis_nama ?? 'Belum teregister' }}</td>
                <td>{{ $view?->hasRuangan?->ruangan_nama ?? '-' }}</td>
                <td>{{ $row->opname_detail_rfid }}</td>
                <td>{{ formatDate($view?->detail_created_at) ?? '-' }}</td>
                <td>{{ $row->opname_detail_transaksi ? \App\Enums\TransactionType::getDescription($row->opname_detail_transaksi) : 'Belum Register' }}</td>
                <td>{{ $view?->detail_status_linen ?? '-' }}</td>
                <td>{{ formatDate($row->opname_detail_updated_at ?? $view?->detail_updated_at) ?? '-' }}</td>
                <td>
                    @if($row->opname_detail_updated_at ?? $view?->detail_updated_at)
                    {{ \Illuminate\Support\Carbon::parse($row->opname_detail_updated_at ?? $view?->detail_updated_at)->diffInDays(now()) }} Hari
                    @else
                    0 Hari
                    @endif
                </td>
                <td>{{ $row->opname_detail_scan_by ?? '-' }}</td>
            </tr>
            @empty
            <tr><td colspan="10" style="text-align:center;">Tidak ada data.</td></tr>
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
