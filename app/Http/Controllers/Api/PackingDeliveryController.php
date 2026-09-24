<?php

namespace App\Http\Controllers\Api;

use App\Enums\LogType;
use App\Enums\TransactionType;
use App\Http\Controllers\Controller;
use App\Models\DetailLinen;
use App\Models\Outstanding;
use App\Models\Rs;
use App\Models\Ruangan;
use App\Models\Transaksi;
use App\Models\User;
use App\Support\DashboardCache;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Plugins\Notes;

class PackingDeliveryController extends Controller
{
    // =========================================================================
    // 1) PACKING — Outstanding QC/GUDANG → PACKING + cetak barcode
    // =========================================================================

    public function packing(Request $request)
    {
        if ($err = $this->validatePacking($request)) {
            return $err;
        }
        User::ensureRsAccess((int) $request->input('rs_id'));

        $input = $this->normalizePacking($request);

        if ($err = $this->assertReadyForPacking($input)) {
            return $err;
        }
        if ($err = $this->assertOwnership($input)) {
            return $err;
        }
        if ($err = $this->assertNotClean($input)) {
            return $err;
        }
        if ($err = $this->assertBarcodeLimit($input)) {
            return $err;
        }

        return $this->executePacking($input);
    }

    private function validatePacking(Request $request)
    {
        $v = Validator::make($request->all(), [
            'rfid' => 'required|array|min:1',
            'rfid.*' => 'required|string',
            'rs_id' => 'required|integer|exists:rs,rs_id',
            'ruangan_id' => 'required|integer|exists:ruangan,ruangan_id',
            'status_transaksi' => 'required|string',
        ], [
            'rfid.required' => 'Kirim minimal satu RFID.',
            'rs_id.required' => 'RS wajib dipilih.',
            'ruangan_id.required' => 'Ruangan wajib dipilih.',
            'status_transaksi.required' => 'Status transaksi wajib diisi.',
        ]);
        if ($v->fails()) {
            return Notes::validation($v->errors()->first(), $v->errors()->toArray());
        }

        return null;
    }

    private function normalizePacking(Request $request): array
    {
        $rfids = collect($request->input('rfid'))->filter()->unique()->values()->all();
        $status = $request->input('status_transaksi');
        if ($status === TransactionType::BERSIH) {
            $status = TransactionType::KOTOR;
        }

        return [
            'rfids' => $rfids,
            'rsId' => (int) $request->input('rs_id'),
            'ruanganId' => (int) $request->input('ruangan_id'),
            'status' => $status,
        ];
    }

    private function assertReadyForPacking(array $input)
    {
        $count = Outstanding::whereIn('outstanding_rfid', $input['rfids'])
            ->where('outstanding_status_transaksi', $input['status'])
            ->where('outstanding_status_proses', '!=', 'PACKING')
            ->count();
        if ($count != count($input['rfids'])) {
            return Notes::validation('RFID dengan status ready packing tidak ditemukan!', ['rfid' => ['RFID dengan status ready packing tidak ditemukan!']]);
        }

        return null;
    }

    private function assertOwnership(array $input)
    {
        try {
            $total = count($input['rfids']);
            $check = DB::table('config_linen')->where('rs_id', $input['rsId'])->whereIn('detail_rfid', $input['rfids'])->count();
            $hasConfig = DB::table('config_linen')->whereIn('detail_rfid', $input['rfids'])->exists();
            if ($hasConfig && $check != $total) {
                return Notes::validation('Status Kepemilikan RFID Bermasalah!', ['rfid' => ['Status Kepemilikan RFID Bermasalah!']]);
            }
        } catch (\Throwable $e) {
        }

        return null;
    }

    private function assertNotClean(array $input)
    {
        $count = DetailLinen::whereIn('detail_rfid', $input['rfids'])->where('detail_status_linen', TransactionType::BERSIH)->count();
        if ($count > 0) {
            return Notes::validation('Status RFID sudah bersih!', ['rfid' => ['Status RFID sudah bersih!']]);
        }

        return null;
    }

    private function assertBarcodeLimit(array $input)
    {
        $max = (int) env('TRANSACTION_BARCODE_MAXIMAL', 10);
        if (count($input['rfids']) > $max) {
            return Notes::validation('RFID maksimal '.$max, ['rfid' => ['RFID maksimal '.$max]]);
        }

        return null;
    }

    private function executePacking(array $input)
    {
        DB::beginTransaction();
        try {
            $code = generatePackingCode();
            $date = now()->format('Y-m-d H:i:s');
            $userId = auth()->id();
            $userName = auth()->user()->name ?? 'System';

            $this->updateOutstandingToPacking($input, $date, $userId);
            $this->insertBersihPacking($input, $code, $date, $userId);
            $this->insertCetakPacking($input, $code, $userName);
            $this->logPacking($input, $code);

            $report = $this->buildPackingReport($input, $code, $date, $userName);

            DB::commit();
            DashboardCache::flush();

            return Notes::data($report);
        } catch (\Throwable $th) {
            DB::rollBack();

            return Notes::error($th->getMessage());
        }
    }

    private function updateOutstandingToPacking(array $input, string $date, $userId): void
    {
        Outstanding::whereIn('outstanding_rfid', $input['rfids'])->update([
            'outstanding_rs_scan' => $input['rsId'],
            'outstanding_id_ruangan' => $input['ruanganId'],
            'outstanding_status_proses' => 'PACKING',
            'outstanding_updated_at' => $date,
            'outstanding_updated_by' => $userId,
            'outstanding_status_hilang' => 'NORMAL',
            'outstanding_hilang_created_at' => null,
            'outstanding_pending_created_at' => null,
        ]);
    }

    private function insertBersihPacking(array $input, string $code, string $date, $userId): void
    {
        foreach ($input['rfids'] as $rfid) {
            DB::table('bersih')->insert([
                'bersih_rfid' => $rfid,
                'bersih_status' => $this->bersihStatus($input['status']),
                'bersih_id_rs' => $input['rsId'],
                'bersih_id_ruangan' => $input['ruanganId'],
                'bersih_barcode' => strtoupper($code),
                'bersih_delivery' => null,
                'bersih_created_at' => $date,
                'bersih_updated_at' => $date,
                'bersih_created_by' => $userId,
                'bersih_updated_by' => $userId,
                'bersih_report' => null,
            ]);
        }
    }

    private function bersihStatus(string $status): string
    {
        return match ($status) {
            TransactionType::REJECT => 'REJECT',
            TransactionType::REWASH => 'REWASH',
            TransactionType::REGISTER => 'REGISTER',
            default => 'BERSIH',
        };
    }

    private function insertCetakPacking(array $input, string $code, string $userName): void
    {
        DB::table('cetak')->insert([
            'cetak_code' => $code,
            'cetak_date' => date('Y-m-d'),
            'cetak_user' => $userName,
            'cetak_id_rs' => $input['rsId'],
            'cetak_id_ruangan' => $input['ruanganId'],
            'cetak_type' => 1,
            'cetak_barcode' => $code,
            'cetak_delivery' => null,
            'cetak_rfids' => json_encode(array_values($input['rfids'])),
        ]);
    }

    private function logPacking(array $input, string $code): void
    {
        // ponytail: 1 log per RFID (subject = DetailLinen) agar terlacak
        // di filter RFID activity-log, bukan 1 log batch.
        try {
            $subjects = DetailLinen::whereIn('detail_rfid', $input['rfids'])->get()->keyBy('detail_rfid');
            foreach ($input['rfids'] as $rfid) {
                $log = activity(LogType::PACKING)
                    ->causedBy(auth()->user())
                    ->withProperties(['rfid' => $rfid, 'rs_id' => $input['rsId'], 'code' => $code]);
                if ($subjects->has($rfid)) {
                    $log->performedOn($subjects->get($rfid));
                }
                $log->log("Packing RFID {$rfid} ke RS {$input['rsId']} ({$code})");
            }
        } catch (\Throwable $e) {
        }
    }

    private function buildPackingReport(array $input, string $code, string $date, string $userName): array
    {
        $details = DetailLinen::with(['hasJenis', 'hasRs', 'hasRuangan'])->whereIn('detail_rfid', $input['rfids'])->get();
        $grouped = $details->groupBy(fn ($item) => $item->detail_id_jenis.'#'.$item->detail_id_ruangan);

        $report = [];
        $no = 1;
        foreach ($grouped as $items) {
            $first = $items->first();
            $report[] = [
                'id' => $no,
                'code' => $code,
                'tgl' => Carbon::parse($date)->format('d/M/Y'),
                'rs' => $first->hasRs?->rs_nama ?? Rs::find($input['rsId'])?->rs_nama ?? '',
                'nama' => $first->hasJenis?->jenis_nama ?? '',
                'lokasi' => $first->hasRuangan?->ruangan_nama ?? Ruangan::find($input['ruanganId'])?->ruangan_nama ?? '',
                'status' => $input['status'],
                'user' => $userName,
                'total' => $items->count(),
            ];
            $no++;
        }

        if (empty($report)) {
            $report[] = [
                'id' => 1,
                'code' => $code,
                'tgl' => Carbon::parse($date)->format('d/M/Y'),
                'rs' => Rs::find($input['rsId'])?->rs_nama ?? '',
                'nama' => '-',
                'lokasi' => Ruangan::find($input['ruanganId'])?->ruangan_nama ?? '',
                'status' => $input['status'],
                'user' => $userName,
                'total' => count($input['rfids']),
            ];
        }

        return $report;
    }

    // =========================================================================
    // 2) DELIVERY — PACKING → BERSIH (hapus outstanding, update detail, cetak+bersih)
    // =========================================================================

    public function delivery(Request $request)
    {
        if ($err = $this->validateDelivery($request)) {
            return $err;
        }

        $input = $this->normalizeDelivery($request);

        if ($err = $this->assertPackingExists($input)) {
            return $err;
        }

        return $this->executeDelivery($input);
    }

    private function validateDelivery(Request $request)
    {
        $v = Validator::make($request->all(), [
            'rs_id' => 'required|integer|exists:rs,rs_id',
            'status_transaksi' => 'required|string',
        ], [
            'rs_id.required' => 'RS wajib dipilih.',
            'status_transaksi.required' => 'Status transaksi wajib diisi.',
        ]);
        if ($v->fails()) {
            User::ensureRsAccess((int) $request->input('rs_id'));

            return Notes::validation($v->errors()->first(), $v->errors()->toArray());
        }

        return null;
    }

    private function normalizeDelivery(Request $request): array
    {
        $status = $request->input('status_transaksi');
        if ($status === TransactionType::BERSIH) {
            $status = TransactionType::KOTOR;
        }

        return [
            'rsId' => (int) $request->input('rs_id'),
            'status' => $status,
        ];
    }

    private function assertPackingExists(array $input)
    {
        $count = Outstanding::where('outstanding_rs_scan', $input['rsId'])
            ->where('outstanding_status_proses', 'PACKING')
            ->where('outstanding_status_transaksi', $input['status'])
            ->count();
        if ($count == 0) {
            return Notes::validation('RFID belum ada yang di packing!', ['rfid' => ['RFID belum ada yang di packing!']]);
        }

        return null;
    }

    private function executeDelivery(array $input)
    {
        DB::beginTransaction();
        try {
            $code = $this->generateDeliveryCode($input);
            [$date, $reportDate] = $this->calculateReportDate();
            $rfids = $this->collectPackingRfids($input);

            $this->updateDetailRuangan($input, $rfids);
            $this->updateDetailsToBersih($input, $rfids, $date, $reportDate);
            $this->insertCetakDelivery($input, $code, $rfids);
            $this->updateBersihRows($input, $code, $rfids, $date, $reportDate);
            $this->deleteOutstanding($rfids);
            $this->updatePending($input, $rfids, $code, $reportDate);
            $this->logDelivery($input, $code, $rfids);

            $report = $this->buildDeliveryReport($input, $code, $rfids, $reportDate);

            DB::commit();
            DashboardCache::flush();

            return Notes::data($report);
        } catch (\Throwable $th) {
            DB::rollBack();

            return Notes::error($th->getMessage());
        }
    }

    private function generateDeliveryCode(array $input): string
    {
        $codeRs = Rs::find($input['rsId'])?->rs_code ?? 'RS';
        $prefix = match ($input['status']) {
            TransactionType::REJECT, TransactionType::RETUR => env('CODE_DELIVERY_RETUR', 'RJK'),
            TransactionType::REWASH => env('CODE_DELIVERY_REWASH', 'RWS'),
            default => env('CODE_DELIVERY_BERSIH', 'BSH'),
        };

        return generateDeliveryCode($codeRs, $prefix);
    }

    private function calculateReportDate(): array
    {
        $date = now();
        $start = Carbon::createFromFormat('Y-m-d H:i', date('Y-m-d').' 13:00');
        $end = Carbon::createFromFormat('Y-m-d H:i:s', date('Y-m-d').' 23:59:59');
        $reportDate = Carbon::now()->between($start, $end) ? Carbon::now()->addDay(1) : Carbon::now();

        return [$date, $reportDate];
    }

    // allow CarbonImmutable (now() di Laravel 13 bisa CarbonImmutable)

    private function collectPackingRfids(array $input): array
    {
        return Outstanding::where('outstanding_rs_scan', $input['rsId'])
            ->where('outstanding_status_proses', 'PACKING')
            ->where('outstanding_status_transaksi', $input['status'])
            ->pluck('outstanding_rfid')->all();
    }

    private function updateDetailRuangan(array $input, array $rfids): void
    {
        $grouped = Outstanding::where('outstanding_rs_scan', $input['rsId'])
            ->where('outstanding_status_proses', 'PACKING')
            ->where('outstanding_status_transaksi', $input['status'])
            ->get()->groupBy('outstanding_id_ruangan');
        foreach ($grouped as $locId => $items) {
            DetailLinen::whereIn('detail_rfid', $items->pluck('outstanding_rfid')->all())->update(['detail_id_ruangan' => $locId]);
        }
    }

    private function updateDetailsToBersih(array $input, array $rfids, $date, $reportDate): void
    {
        $update = [
            'detail_id_rs' => $input['rsId'],
            'detail_status_linen' => TransactionType::BERSIH,
            'detail_updated_at' => $date->format('Y-m-d H:i:s'),
            'detail_report' => $reportDate->format('Y-m-d'),
            'detail_updated_by' => auth()->id(),
        ];
        $norm = strtoupper((string) $input['status']);
        if ($norm === TransactionType::REWASH) {
            $update['detail_total_rewash'] = DB::raw('COALESCE(detail_total_rewash, 0) + 1');
        } elseif ($norm === TransactionType::REJECT || $norm === 'RETUR') {
            $update['detail_total_reject'] = DB::raw('COALESCE(detail_total_reject, 0) + 1');
        } else {
            $update['detail_total_bersih'] = DB::raw('COALESCE(detail_total_bersih, 0) + 1');
        }
        DetailLinen::whereIn('detail_rfid', $rfids)->update($update);
    }

    private function insertCetakDelivery(array $input, string $code, array $rfids): void
    {
        DB::table('cetak')->insert([
            'cetak_code' => $code,
            'cetak_date' => date('Y-m-d'),
            'cetak_user' => auth()->user()->name ?? null,
            'cetak_id_rs' => $input['rsId'],
            'cetak_id_ruangan' => null,
            'cetak_type' => 2,
            'cetak_barcode' => null,
            'cetak_delivery' => $code,
            'cetak_rfids' => json_encode(array_values($rfids)),
        ]);
    }

    private function updateBersihRows(array $input, string $code, array $rfids, $date, $reportDate): void
    {
        DB::table('bersih')
            ->where('bersih_id_rs', $input['rsId'])
            ->where('bersih_status', $this->bersihStatus($input['status']))
            ->whereNull('bersih_delivery')
            ->whereIn('bersih_rfid', $rfids)
            ->update([
                'bersih_delivery' => strtoupper($code),
                'bersih_report' => $reportDate->format('Y-m-d'),
                'bersih_updated_at' => $date->format('Y-m-d H:i:s'),
                'bersih_updated_by' => auth()->id(),
            ]);
    }

    private function deleteOutstanding(array $rfids): void
    {
        Outstanding::whereIn('outstanding_rfid', $rfids)->delete();
    }

    private function updatePending(array $input, array $rfids, string $code, $reportDate): void
    {
        try {
            if (Schema::hasTable('pending')) {
                DB::table('pending')->where('pending_transaksi', '!=', TransactionType::BERSIH)->whereIn('pending_rfid', $rfids)->update([
                    'pending_bersih_by' => auth()->id(),
                    'pending_bersih_at' => $reportDate->format('Y-m-d H:i:s'),
                    'pending_updated_at' => $reportDate->format('Y-m-d H:i:s'),
                    'pending_delivery' => $code,
                ]);
            }
        } catch (\Throwable $e) {
        }
    }

    private function logDelivery(array $input, string $code, array $rfids): void
    {
        // ponytail: 1 log per RFID (subject = DetailLinen) agar terlacak
        // di filter RFID activity-log, bukan 1 log batch.
        try {
            $subjects = DetailLinen::whereIn('detail_rfid', $rfids)->get()->keyBy('detail_rfid');
            foreach ($rfids as $rfid) {
                $log = activity(LogType::DELIVERY)
                    ->causedBy(auth()->user())
                    ->withProperties(['rfid' => $rfid, 'rs_id' => $input['rsId'], 'code' => $code]);
                if ($subjects->has($rfid)) {
                    $log->performedOn($subjects->get($rfid));
                }
                $log->log("Delivery {$code} RFID {$rfid}");
            }
        } catch (\Throwable $e) {
        }
    }

    private function buildDeliveryReport(array $input, string $code, array $rfids, $reportDate): array
    {
        $details = DetailLinen::with(['hasJenis', 'hasRs', 'hasRuangan'])->whereIn('detail_rfid', $rfids)->get();
        $grouped = $details->groupBy(fn ($item) => $item->detail_id_jenis.'#'.$item->detail_id_ruangan);

        $report = [];
        $no = 1;
        foreach ($grouped as $items) {
            $first = $items->first();
            $report[] = [
                'id' => $no,
                'code' => $code,
                'tgl' => $reportDate->format('d/M/Y'),
                'rs' => $first->hasRs?->rs_nama ?? Rs::find($input['rsId'])?->rs_nama ?? '',
                'nama' => $first->hasJenis?->jenis_nama ?? '',
                'lokasi' => $first->hasRuangan?->ruangan_nama ?? '',
                'status' => TransactionType::BERSIH,
                'user' => auth()->user()->name ?? '',
                'total' => $items->count(),
            ];
            $no++;
        }

        if (empty($report)) {
            $report[] = [
                'id' => 1,
                'code' => $code,
                'tgl' => $reportDate->format('d/M/Y'),
                'rs' => Rs::find($input['rsId'])?->rs_nama ?? '',
                'nama' => '-',
                'lokasi' => '',
                'status' => TransactionType::BERSIH,
                'user' => auth()->user()->name ?? '',
                'total' => count($rfids),
            ];
        }

        return $report;
    }

    // =========================================================================
    // 3) LIST & PRINT — riwayat cetak & reprint
    // =========================================================================

    public function listPacking($rsid)
    {
        $data = DB::table('cetak')->select(['cetak_code'])->where('cetak_id_rs', $rsid)->where('cetak_type', 1)->where('cetak_date', '>=', now()->subDays(30)->format('Y-m-d'))->orderBy('cetak_id', 'desc')->get();

        return Notes::data($data);
    }

    public function listDelivery($rsid)
    {
        $q = DB::table('cetak')->select(['cetak_code'])->where('cetak_id_rs', $rsid)->where('cetak_type', 2)->where('cetak_date', '>=', now()->subDays(30)->format('Y-m-d'))->orderBy('cetak_id', 'desc');
        if (request()->get('tgl')) {
            $q->where('cetak_date', '=', request()->get('tgl'));
        }

        return Notes::data($q->get());
    }

    public function printPacking($code)
    {
        $rfids = $this->rfidsFromCetak($code);
        if (empty($rfids)) {
            $rfids = Outstanding::where('outstanding_key', $code)->pluck('outstanding_rfid')->all();
        }
        if (empty($rfids)) {
            $rfids = Transaksi::where('transaksi_key', $code)->pluck('transaksi_rfid')->all();
        }
        $details = DetailLinen::with(['hasJenis', 'hasRs', 'hasRuangan'])->whereIn('detail_rfid', $rfids ?: ['__none__'])->get();

        return Notes::data($details);
    }

    public function printDelivery($code)
    {
        $rfids = $this->rfidsFromCetak($code);
        if (empty($rfids)) {
            $rfids = Transaksi::where('transaksi_key', $code)->pluck('transaksi_rfid')->all();
        }
        $details = DetailLinen::with(['hasJenis', 'hasRs', 'hasRuangan'])->whereIn('detail_rfid', $rfids ?: ['__none__'])->get();

        return Notes::data($details);
    }

    private function rfidsFromCetak(string $code): array
    {
        try {
            $row = DB::table('cetak')->where('cetak_code', $code)->first();
            if (! $row || empty($row->cetak_rfids)) {
                return [];
            }
            $decoded = json_decode($row->cetak_rfids, true);

            return is_array($decoded) ? array_values(array_filter($decoded)) : [];
        } catch (\Throwable $e) {
            return [];
        }
    }

    // =========================================================================
    // 4) TOTALS — dashboard counters
    // =========================================================================

    public function totalDelivery($rsid, $status)
    {
        if ($status === TransactionType::BERSIH) {
            $status = TransactionType::KOTOR;
        }
        $count = Outstanding::where('outstanding_rs_scan', $rsid)->where('outstanding_status_proses', 'PACKING')->where('outstanding_status_transaksi', $status)->count();

        return Notes::data(['total' => $count, 'view_total' => $count]);
    }

    public function totalOutstanding($rsid, $ruangan, $jenis, $transaksi)
    {
        if ($transaksi === TransactionType::BERSIH) {
            $transaksi = TransactionType::KOTOR;
        }
        $q = Outstanding::query()->where('outstanding_rs_scan', $rsid)->where('outstanding_id_ruangan', $ruangan)->where('outstanding_status_transaksi', $transaksi);
        if ($jenis && $jenis != 0) {
            $rfids = DetailLinen::where('detail_id_jenis', $jenis)->pluck('detail_rfid')->all();
            $q->whereIn('outstanding_rfid', $rfids);
        }
        $count = $q->count();

        return Notes::data(['view_total' => $count, 'view_rs_id' => $rsid, 'view_jenis_id' => $jenis, 'view_ruangan_id' => $ruangan, 'view_status' => $transaksi]);
    }

    public function totalBersih($rsid, $ruangan, $jenis, $transaksi)
    {
        $q = DetailLinen::query()->where('detail_id_rs', $rsid)->where('detail_id_ruangan', $ruangan)->where('detail_status_linen', TransactionType::BERSIH);
        if ($jenis && $jenis != 0) {
            $q->where('detail_id_jenis', $jenis);
        }
        $count = $q->count();

        return Notes::data(['view_total' => $count]);
    }
}
