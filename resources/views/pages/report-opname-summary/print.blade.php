<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>REPORT OPNAME SUMMARY - {{ $opname->hasRs?->rs_nama ?? '' }}</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: Arial, Helvetica, sans-serif; font-size: 11px; color: #000; margin: 20px; background: #fff; }
        .header { width: 100%; border: 0; margin-bottom: 10px; }
        .header h3 { margin: 2px 0; white-space: nowrap; }
        .header td { white-space: nowrap; }
        table.data { width: 100%; border-collapse: collapse; border-spacing: 0; margin-bottom: 14px; }
        table.data th, table.data td { border: 1px solid #000; padding: 4px 6px; white-space: nowrap; }
        table.data thead th { background: #eee; text-align: left; }
        table.data td.num, table.data th.num { text-align: right; }
        .table-scroll { overflow-x: auto; -webkit-overflow-scrolling: touch; }
        .footer { width: 100%; margin-top: 20px; border: 0; }
        .print-date, .print-person { text-align: right; }
        .no-print { margin-bottom: 12px; }
        .toolbar {
            position: fixed; top: 16px; right: 16px; z-index: 9999;
            display: flex; flex-wrap: wrap; justify-content: flex-end; gap: 8px;
        }
        .toolbar .btn {
            display: inline-flex; align-items: center; justify-content: center; gap: 6px;
            padding: 8px 20px; font-size: 14px; line-height: 1.2; cursor: pointer;
            font-family: Arial, Helvetica, sans-serif; text-decoration: none;
            border: 1px solid #ccc; border-radius: 6px; background: #fff; color: #111;
            box-shadow: 0 2px 8px rgba(0,0,0,.2); transition: background .15s, box-shadow .15s;
        }
        .toolbar .btn:hover { background: #f3f4f6; }
        .toolbar .btn-export { background: #16a34a; border-color: #16a34a; color: #fff; }
        .toolbar .btn-export:hover { background: #15803d; }

        /* â€”â€” mobile cards (pola users/table.blade.php) â€”â€” */
        .mobile-only { display: none; }
        .kpi-cards { display: none; }
        .day-cards { display: none; }

        @media (max-width: 767px) {
            body { margin: 12px; font-size: 13px; }
            .toolbar {
                top: auto; bottom: 12px; left: 12px; right: 12px;
                background: #fff; border: 1px solid #ddd; border-radius: 10px;
                padding: 10px; box-shadow: 0 4px 16px rgba(0,0,0,.14);
            }
            .toolbar .btn { flex: 1 1 140px; min-height: 44px; }
            .header h3 { white-space: normal; font-size: 14px; line-height: 1.35; }
            .header td { white-space: normal; }
            .header img { width: 64px !important; max-height: 48px !important; }
            .desktop-only { display: none !important; }
            .mobile-only, .kpi-cards, .day-cards { display: block; }
            .print-date, .print-person { text-align: left; }

            .day-card, .kpi-card {
                border: 1px solid #000; border-radius: 10px;
                padding: 12px; margin-bottom: 10px; background: #fff;
            }
            .day-card .row, .kpi-card .row {
                display: flex; justify-content: space-between; align-items: baseline; gap: 12px;
            }
            .day-card .label, .kpi-card .label {
                font-size: 11px; text-transform: uppercase; letter-spacing: .04em; color: #555;
            }
            .day-card .value, .kpi-card .value { font-size: 18px; font-weight: 700; color: #000; }
            .day-card.total, .kpi-card.total { background: #eee; }
            .kpi-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
            .kpi-card.total { grid-column: 1 / -1; }
            .section-title {
                font-size: 12px; font-weight: 700; text-transform: uppercase;
                margin: 14px 0 8px; color: #333; border-bottom: 2px solid #000; padding-bottom: 4px;
            }
        }

        @media print {
            .toolbar, .no-print, .mobile-only, .kpi-cards, .day-cards { display: none !important; }
            .desktop-only { display: block !important; }
            body { margin: 0; }
            table.data { page-break-inside: auto; }
            tr { page-break-inside: avoid; }
        }
    </style>
</head>
<body>

<div class="toolbar no-print">
    <button type="button" class="btn" onclick="window.print()">Cetak / Simpan PDF</button>
    <a class="btn btn-export" href="{{ route('report-opname-summary.getExportExcel', request()->query()) }}">Export Excel</a>
</div>

@php
    $logoUrl = \App\Models\WebsiteSetting::fileUrl(config('website.logo'));

    $data = $details->sortBy('opname_detail_waktu')->values();
    $map = $data->mapToGroups(fn ($item) => [formatDate($item->opname_detail_waktu) => $item]);

    // andalan data.blade.php:
    // tembak_so = ketemu 1 && transaksi != 0
    // hilang_rs = ketemu 0 && BERSIH
    // hilang_warehouse = ketemu 0 && transaksi != BERSIH
    $tembakSo = fn ($rows) => $rows
        ->where('opname_detail_ketemu', 1)
        ->where('opname_detail_transaksi', '!=', 0)
        ->count();
    $hilangRs = fn ($rows) => $rows
        ->where('opname_detail_ketemu', 0)
        ->where('opname_detail_transaksi', \App\Enums\TransactionType::BERSIH)
        ->count();
    $hilangWh = fn ($rows) => $rows
        ->where('opname_detail_ketemu', 0)
        ->where('opname_detail_transaksi', '!=', \App\Enums\TransactionType::BERSIH)
        ->count();

    $subTembak = $tembakSo($data);
    $subHilangRs = $hilangRs($data);
    $subHilangWh = $hilangWh($data);
    $registerCount = $register ?? $data->whereNotNull('opname_detail_transaksi')->count();

    $grandTotal = 0;
    foreach ($map as $rows) {
        $grandTotal += $tembakSo($rows) + $hilangRs($rows) + $hilangWh($rows);
    }
@endphp

<table class="header">
    <tr>
        <td style="vertical-align:middle;">
            <h3><b>REPORT OPNAME SUMMARY</b></h3>
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

<div id="table_data">
    {{-- ========== DESKTOP / PRINT â€” tabel andalan ========== --}}
    <div class="desktop-only">
        <div class="table-scroll">
            <table class="data">
                <thead>
                    <tr>
                        <th width="1">No.</th>
                        <th>TANGGAL</th>
                        <th>SCAN LINEN SAAT SO</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($map as $key => $rows)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $key ?? '' }}</td>
                        <td class="num">{{ $tembakSo($rows) }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="3" style="text-align:center;">Tidak ada data.</td></tr>
                    @endforelse
                    <tr>
                        <td colspan="2">Total Snapshot</td>
                        <td class="num">{{ $subTembak }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <br>

        <div class="table-scroll">
            <table class="data">
                <thead>
                    <tr>
                        <th>Total Register</th>
                        <th>Total Scan Linen</th>
                        <th>Total belum terbaca di Rs</th>
                        <th>Total belum terbaca di Laundry</th>
                        <th>Total Summary</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="num">{{ $registerCount }}</td>
                        <td class="num">{{ $subTembak }}</td>
                        <td class="num">{{ $subHilangRs }}</td>
                        <td class="num">{{ $subHilangWh }}</td>
                        <td class="num">{{ $grandTotal }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    {{-- ========== MOBILE â€” kartu (pola users/table) ========== --}}
    <div class="mobile-only">
        <p class="section-title">Scan Linen Saat SO</p>
        <div class="day-cards">
            @forelse($map as $key => $rows)
            <div class="day-card">
                <div class="row">
                    <span class="label">{{ $loop->iteration }} Â· {{ $key ?? '' }}</span>
                    <span class="value">{{ $tembakSo($rows) }}</span>
                </div>
            </div>
            @empty
            <div class="day-card"><div class="row"><span class="label">Tidak ada data.</span></div></div>
            @endforelse
            <div class="day-card total">
                <div class="row">
                    <span class="label">Total Snapshot</span>
                    <span class="value">{{ $subTembak }}</span>
                </div>
            </div>
        </div>

        <p class="section-title">Summary</p>
        <div class="kpi-cards">
            <div class="kpi-grid">
                <div class="kpi-card">
                    <div class="row" style="display:block;">
                        <p class="label">Total Register</p>
                        <p class="value">{{ $registerCount }}</p>
                    </div>
                </div>
                <div class="kpi-card">
                    <div class="row" style="display:block;">
                        <p class="label">Total Scan Linen</p>
                        <p class="value">{{ $subTembak }}</p>
                    </div>
                </div>
                <div class="kpi-card">
                    <div class="row" style="display:block;">
                        <p class="label">Belum terbaca di Rs</p>
                        <p class="value">{{ $subHilangRs }}</p>
                    </div>
                </div>
                <div class="kpi-card">
                    <div class="row" style="display:block;">
                        <p class="label">Belum terbaca di Laundry</p>
                        <p class="value">{{ $subHilangWh }}</p>
                    </div>
                </div>
                <div class="kpi-card total">
                    <div class="row" style="display:block;">
                        <p class="label">Total Summary</p>
                        <p class="value">{{ $grandTotal }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
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
