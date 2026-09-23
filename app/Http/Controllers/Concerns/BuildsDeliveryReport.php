<?php

namespace App\Http\Controllers\Concerns;

use App\Models\Rs;
use App\Models\WebsiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Report pengiriman (adopsi andalan ReportDetailPengiriman* +
 * ReportSummaryPengiriman*). Sumber utama: tabel `bersih` legacy
 * (kolom bersih_report + bersih_status + DO/barcode per RFID).
 * Ekor aliran baru (cetak, tanpa baris bersih): digabung untuk tanggal
 * di luar jangkauan legacy (disjoint, tanpa dobel).
 *
 * Controller pemakai cukup definisikan konstanta:
 * - KIND          : 'detail' | 'summary'
 * - DELIVERY_TYPE : 'BERSIH' | 'RETUR' | 'REWASH' | 'REGISTER'
 * - TITLE         : judul kop, mis. 'DETAIL PENGIRIMAN LINEN BERSIH'
 * - FILE_PREFIX   : awalan nama file excel, mis. 'detail-pengiriman-bersih'
 *
 * Kolom NO. BARCODE baris ekor dipetakan dari cetak packing (type=1).
 */
trait BuildsDeliveryReport
{
    public function getPrint(Request $request)
    {
        set_time_limit(0);

        $validated = $this->validateDelivery($request);
        $rs = Rs::where('rs_id', $validated['rs_id'])->firstOrFail();

        if (static::KIND === 'summary') {
            $data = $this->summaryRows($validated);

            return $this->views($this->template(), array_merge($this->share(), [
                'title' => static::TITLE,
                'data' => $data,
                'rs' => $rs,
                'start' => $validated['start_delivery'] ?? null,
                'end' => $validated['end_delivery'] ?? null,
            ]));
        }

        $count = $this->countDetailRows($validated);
        $threshold = (int) env('REPORT_CHUNK', 10000);
        if ($threshold > 0 && $count > $threshold) {
            return $this->getExportExcel($request);
        }

        return $this->views($this->template(), array_merge($this->share(), [
            'title' => static::TITLE,
            'data' => $this->detailRows($validated),
            'rs' => $rs,
            'start' => $validated['start_delivery'] ?? null,
            'end' => $validated['end_delivery'] ?? null,
        ]));
    }

    public function getExportExcel(Request $request)
    {
        set_time_limit(0);

        $validated = $this->validateDelivery($request);

        $rs = Rs::where('rs_id', $validated['rs_id'])->firstOrFail();
        $rsNama = $rs->rs_nama ?? 'Semua Rumah Sakit';
        $logoUrl = WebsiteSetting::fileUrl(config('website.logo'));
        $logoAbs = $logoUrl ? url($logoUrl) : null;
        $periode = (formatDate($validated['start_delivery'] ?? null) ?? '-')
            .' - '.(formatDate($validated['end_delivery'] ?? null) ?? '-');
        $title = static::TITLE;
        $kind = static::KIND;

        $filename = static::FILE_PREFIX.'-'.now()->format('Ymd-His').'.xls';

        if ($kind === 'summary') {
            $data = $this->summaryRows($validated);

            return response()
                ->view($this->template('excel'), array_merge($this->share(), [
                    'title' => $title,
                    'data' => $data,
                    'rs' => $rs,
                    'start' => $validated['start_delivery'] ?? null,
                    'end' => $validated['end_delivery'] ?? null,
                ]), 200, [
                    'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
                    'Content-Disposition' => 'attachment; filename="'.$filename.'"',
                ]);
        }

        return response()->streamDownload(function () use ($validated, $rsNama, $logoAbs, $periode, $title) {
            echo '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel">';
            echo '<head><meta charset="UTF-8"></head><body>';
            echo '<table><tr><td colspan="7"><b>'.e($title).'</b><br><b>RUMAH SAKIT : '.e($rsNama).'</b><br><b>Periode : '.e($periode).'</b></td>';
            echo '<td colspan="2" style="text-align:right;">';
            if ($logoAbs) {
                echo '<img src="'.e($logoAbs).'" alt="Logo" height="60" width="90">';
            }
            echo '</td></tr></table><br>';
            echo '<table border="1"><thead><tr>'
                .'<th>No.</th><th>NO. DO</th><th>NO. BARCODE</th><th>NO. RFID</th>'
                .'<th>LINEN</th><th>RUMAH SAKIT</th><th>RUANGAN</th>'
                .'<th>TANGGAL DO</th><th>OPERATOR</th>'
                .'</tr></thead><tbody>';

            $no = 0;
            foreach ($this->iterateDetailRows($validated) as $row) {
                $no++;
                echo '<tr><td>'.$no.'</td>'
                    .'<td>'.e($row['do']).'</td>'
                    .'<td>'.e($row['barcode']).'</td>'
                    .'<td>'.e($row['rfid']).'</td>'
                    .'<td>'.e($row['linen']).'</td>'
                    .'<td>'.e($row['rs']).'</td>'
                    .'<td>'.e($row['ruangan']).'</td>'
                    .'<td>'.e($row['tanggal']).'</td>'
                    .'<td>'.e($row['operator']).'</td></tr>';

                if ($no % 1000 === 0) {
                    flush();
                }
            }

            echo '</tbody></table></body></html>';
        }, $filename, [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }

    private function validateDelivery(Request $request): array
    {
        return $request->validate([
            'rs_id' => 'required|integer|exists:rs,rs_id',
            'start_delivery' => 'nullable|date',
            'end_delivery' => 'nullable|date|after_or_equal:start_delivery',
        ]);
    }

    /**
     * Status bersih_* untuk DELIVERY_TYPE (kolom bersih_status legacy).
     */
    private function legacyStatus(): string
    {
        return match (static::DELIVERY_TYPE) {
            'RETUR' => 'REJECT',
            'REWASH' => 'REWASH',
            'REGISTER' => 'REGISTER',
            default => 'BERSIH',
        };
    }

    /**
     * Batas akhir era legacy per RS — ekor cetak hanya untuk tanggal sesudahnya.
     */
    private function legacyMaxReport(int $rsId): ?string
    {
        $max = DB::table('bersih')->where('bersih.bersih_id_rs', $rsId)->max('bersih.bersih_report');

        return $max ? substr((string) $max, 0, 10) : null;
    }

    private function applyLegacyRange($query, array $filter)
    {
        return $query
            ->when(! empty($filter['start_delivery']), fn ($q) => $q->whereDate('bersih.bersih_report', '>=', $filter['start_delivery']))
            ->when(! empty($filter['end_delivery']), fn ($q) => $q->whereDate('bersih.bersih_report', '<=', $filter['end_delivery']));
    }

    /**
     * Ekor aliran baru: batch delivery (cetak type=2) sesuai prefix tipe,
     * hanya untuk tanggal di luar jangkauan legacy (disjoint).
     * REGISTER tidak terdeteksi dari kode → ekor kosong.
     */
    private function deliveryBatches(array $filter)
    {
        if (static::DELIVERY_TYPE === 'REGISTER') {
            return DB::table('cetak')->whereRaw('1 = 0')->select(['cetak.*']);
        }

        $legacyMax = $this->legacyMaxReport((int) $filter['rs_id']);

        return DB::table('cetak')
            ->where('cetak.cetak_type', 2)
            ->where('cetak.cetak_id_rs', $filter['rs_id'])
            ->when(! empty($filter['start_delivery']), fn ($q) => $q->whereDate('cetak.cetak_date', '>=', $filter['start_delivery']))
            ->when(! empty($filter['end_delivery']), fn ($q) => $q->whereDate('cetak.cetak_date', '<=', $filter['end_delivery']))
            ->when($legacyMax, fn ($q) => $q->whereDate('cetak.cetak_date', '>', $legacyMax))
            ->where('cetak.cetak_delivery', 'like', $this->resolvePrefix().'\\%')
            ->orderBy('cetak.cetak_date')
            ->orderBy('cetak.cetak_id')
            ->select(['cetak.*']);
    }

    /**
     * Prefix kode delivery — env yang sama dengan PackingDeliveryController.
     */
    private function resolvePrefix(): string
    {
        return match (static::DELIVERY_TYPE) {
            'RETUR' => (string) env('CODE_DELIVERY_RETUR', 'RJK'),
            'REWASH' => (string) env('CODE_DELIVERY_REWASH', 'RWS'),
            default => (string) env('CODE_DELIVERY_BERSIH', 'BSH'),
        };
    }

    private function summaryRows(array $filter): array
    {
        $rsNama = Rs::where('rs_id', $filter['rs_id'])->value('rs_nama');
        $out = [];

        // Legacy: satu baris per DO dari tabel bersih.
        $legacy = $this->applyLegacyRange(
            DB::table('bersih')
                ->leftJoin('users', 'users.id', '=', 'bersih.bersih_created_by')
                ->where('bersih.bersih_status', $this->legacyStatus())
                ->where('bersih.bersih_id_rs', $filter['rs_id']),
            $filter
        )
            ->groupBy('bersih.bersih_delivery')
            ->selectRaw('bersih.bersih_delivery as do, MIN(bersih.bersih_report) as tanggal_raw, COUNT(*) as total')
            ->selectRaw("SUBSTRING_INDEX(GROUP_CONCAT(users.name ORDER BY bersih.bersih_id SEPARATOR '|'), '|', 1) as operator")
            ->orderBy('tanggal_raw')
            ->orderBy('do')
            ->get();

        foreach ($legacy as $row) {
            $out[] = [
                'do' => $row->do ?? '-',
                'rs' => $rsNama ?? '-',
                'total' => (int) $row->total,
                'tanggal' => $row->tanggal_raw ? formatDate($row->tanggal_raw) ?? '-' : '-',
                'operator' => $row->operator ?? '-',
            ];
        }

        // Ekor aliran baru: per batch cetak berisi RFID.
        foreach ($this->deliveryBatches($filter)->cursor() as $batch) {
            $rfids = $this->decodeRfids($batch->cetak_rfids);
            if ($rfids === []) {
                continue;
            }
            $out[] = [
                'do' => $batch->cetak_delivery ?? $batch->cetak_code,
                'rs' => $rsNama ?? '-',
                'total' => count($rfids),
                'tanggal' => $batch->cetak_date ? formatDate($batch->cetak_date) ?? '-' : '-',
                'operator' => $batch->cetak_user ?? '-',
            ];
        }

        return $out;
    }

    private function countDetailRows(array $filter): int
    {
        $total = (int) $this->applyLegacyRange(
            DB::table('bersih')
                ->where('bersih.bersih_status', $this->legacyStatus())
                ->where('bersih.bersih_id_rs', $filter['rs_id']),
            $filter
        )->count();

        $batches = $this->deliveryBatches($filter)->get();
        foreach ($batches as $batch) {
            $total += count($this->decodeRfids($batch->cetak_rfids));
        }

        return $total;
    }

    /**
     * Baris detail per RFID (generator — hemat memory): legacy dari tabel
     * bersih dulu, dilanjut ekor batch cetak berisi RFID.
     *
     * @return iterable<array{do: string, barcode: string, rfid: string, linen: string, rs: string, ruangan: string, tanggal: string, operator: string}>
     */
    private function iterateDetailRows(array $filter): iterable
    {
        $legacy = $this->applyLegacyRange(
            DB::table('bersih')
                ->leftJoin('detail_linen', 'detail_linen.detail_rfid', '=', 'bersih.bersih_rfid')
                ->leftJoin('jenis_linen', 'jenis_linen.jenis_id', '=', 'detail_linen.detail_id_jenis')
                ->leftJoin('ruangan', 'ruangan.ruangan_id', '=', 'bersih.bersih_id_ruangan')
                ->leftJoin('rs', 'rs.rs_id', '=', 'bersih.bersih_id_rs')
                ->leftJoin('users', 'users.id', '=', 'bersih.bersih_created_by')
                ->where('bersih.bersih_status', $this->legacyStatus())
                ->where('bersih.bersih_id_rs', $filter['rs_id']),
            $filter
        )
            ->orderBy('bersih.bersih_report')
            ->orderBy('bersih.bersih_id')
            ->select([
                'bersih.bersih_rfid as rfid',
                'bersih.bersih_barcode as barcode',
                'bersih.bersih_delivery as do',
                'bersih.bersih_report as tanggal_raw',
                'jenis_linen.jenis_nama as jenis_nama',
                'rs.rs_nama as rs_nama',
                'ruangan.ruangan_nama as ruangan_nama',
                'users.name as operator_nama',
            ])
            ->cursor();

        foreach ($legacy as $row) {
            yield [
                'do' => $row->do ?? '-',
                'barcode' => $row->barcode ?? '-',
                'rfid' => $row->rfid,
                'linen' => $row->jenis_nama ?? '-',
                'rs' => $row->rs_nama ?? '-',
                'ruangan' => $row->ruangan_nama ?? '-',
                'tanggal' => $row->tanggal_raw ? formatDate($row->tanggal_raw) ?? '-' : '-',
                'operator' => $row->operator_nama ?? '-',
            ];
        }

        $batches = $this->deliveryBatches($filter)->get();

        if ($batches->isNotEmpty()) {
            $barcodeMap = $this->barcodeMap($filter);

            $allRfids = [];
            foreach ($batches as $batch) {
                foreach ($this->decodeRfids($batch->cetak_rfids) as $rfid) {
                    $allRfids[$rfid] = true;
                }
            }

            $detailMap = [];
            if ($allRfids !== []) {
                $details = DB::table('detail_linen')
                    ->leftJoin('jenis_linen', 'jenis_linen.jenis_id', '=', 'detail_linen.detail_id_jenis')
                    ->leftJoin('ruangan', 'ruangan.ruangan_id', '=', 'detail_linen.detail_id_ruangan')
                    ->leftJoin('rs', 'rs.rs_id', '=', 'detail_linen.detail_id_rs')
                    ->whereIn('detail_linen.detail_rfid', array_keys($allRfids))
                    ->select([
                        'detail_linen.detail_rfid',
                        'jenis_linen.jenis_nama as jenis_nama',
                        'rs.rs_nama as rs_nama',
                        'ruangan.ruangan_nama as ruangan_nama',
                    ])
                    ->get();
                foreach ($details as $d) {
                    $detailMap[$d->detail_rfid] = $d;
                }
            }

            foreach ($batches as $batch) {
                $rfids = $this->decodeRfids($batch->cetak_rfids);
                if ($rfids === []) {
                    continue;
                }
                $do = $batch->cetak_delivery ?? $batch->cetak_code;
                $tanggal = $batch->cetak_date ? formatDate($batch->cetak_date) ?? '-' : '-';
                $operator = $batch->cetak_user ?? '-';

                foreach ($rfids as $rfid) {
                    $d = $detailMap[$rfid] ?? null;

                    yield [
                        'do' => $do,
                        'barcode' => $barcodeMap[$rfid] ?? '-',
                        'rfid' => $rfid,
                        'linen' => $d->jenis_nama ?? '-',
                        'rs' => $d->rs_nama ?? '-',
                        'ruangan' => $d->ruangan_nama ?? '-',
                        'tanggal' => $tanggal,
                        'operator' => $operator,
                    ];
                }
            }
        }
    }

    private function detailRows(array $filter): array
    {
        return iterator_to_array($this->iterateDetailRows($filter), false);
    }

    /**
     * Peta RFID → kode barcode packing (cetak type=1) satu RS.
     */
    private function barcodeMap(array $filter): array
    {
        $map = [];
        $q = DB::table('cetak')
            ->where('cetak.cetak_type', 1)
            ->where('cetak.cetak_id_rs', $filter['rs_id'])
            ->when(! empty($filter['end_delivery']), fn ($qq) => $qq->whereDate('cetak.cetak_date', '<=', $filter['end_delivery']))
            ->orderBy('cetak.cetak_id')
            ->cursor();

        foreach ($q as $pack) {
            $code = $pack->cetak_barcode ?? $pack->cetak_code;
            foreach ($this->decodeRfids($pack->cetak_rfids) as $rfid) {
                $map[$rfid] = $map[$rfid] ?? $code;
            }
        }

        return $map;
    }

    private function decodeRfids($raw): array
    {
        if (empty($raw)) {
            return [];
        }
        if (is_array($raw)) {
            return array_values(array_filter($raw));
        }
        $decoded = json_decode((string) $raw, true);

        return is_array($decoded) ? array_values(array_filter($decoded)) : [];
    }
}
