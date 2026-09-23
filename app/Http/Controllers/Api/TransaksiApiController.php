<?php

namespace App\Http\Controllers\Api;

use App\Enums\TransactionType;
use App\Http\Controllers\Controller;
use App\Models\DetailLinen;
use App\Models\Outstanding;
use App\Models\Transaksi;
use App\Models\User;
use App\Support\DashboardCache;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Plugins\Notes;

/**
 * RFID dirty/return/rewash scan — entry from desktop.
 * Flow: validate → normalize rfids → load context → build rows → persist → refresh → response.
 */
class TransaksiApiController extends Controller
{
    // =========================================================================
    // 1) ENTRY — dari Route::post('/transaksi/{type}') dan alias legacy
    // =========================================================================

    public function kotor(Request $request)
    {
        return $this->transaction($request, 'KOTOR');
    }

    public function retur(Request $request)
    {
        return $this->transaction($request, 'RETUR');
    }

    public function rewash(Request $request)
    {
        return $this->transaction($request, 'REWASH');
    }

    public function transaction(Request $request, string $type)
    {
        $status = $this->resolveStatus($type);
        if ($status === null) {
            return Notes::validation('Tipe transaksi tidak valid.', ['type' => ['Tipe transaksi tidak valid.']]);
        }

        $this->validateTransactionRequest($request);
        User::ensureRsAccess((int) $request->input('rs_id'));

        return $this->handle($request, $status, 'SCAN');
    }

    // =========================================================================
    // 2) HANDLE — orkestrasi transaksi + outstanding (order = execution)
    // =========================================================================

    private function handle(Request $request, string $statusTransaksi, string $statusProcess)
    {
        try {
            DB::beginTransaction();

            $ctx = $this->prepareContext($request);
            $rows = $this->buildRows($ctx, $statusTransaksi, $statusProcess);

            $this->persistTransactions($rows['transaksi']);
            $this->persistOutstanding($rows['outstanding'], $ctx['outstandingExisting'], $ctx['now'], $ctx['userId']);
            $this->refreshExistingOutstanding($ctx, $statusTransaksi, $statusProcess);
            $this->markDetailsAsKotor($rows['toKotor'], $ctx['now']);
            $this->logTransactions($rows['transaksi'], $request);

            DB::commit();
            DashboardCache::flush();

            return $this->successResponse($ctx, $statusTransaksi, $rows['transaksi']);
        } catch (\Throwable $th) {
            DB::rollBack();

            return Notes::failed(500, $th->getMessage());
        }
    }

    // =========================================================================
    // 3) PREPARE — input & context
    // =========================================================================

    private function resolveStatus(string $type): ?string
    {
        $type = strtoupper($type);
        $map = [
            'KOTOR' => TransactionType::KOTOR,
            'RETUR' => TransactionType::REJECT,
            'REJECT' => TransactionType::REJECT,
            'REWASH' => TransactionType::REWASH,
        ];

        return $map[$type] ?? null;
    }

    private function validateTransactionRequest(Request $request): void
    {
        $request->validate([
            'rfid' => 'required|array|min:1',
            'rfid.*' => 'required|string',
            'rs_id' => 'required|integer|exists:rs,rs_id',
            'key' => 'required|string|max:255',
        ]);
    }

    private function prepareContext(Request $request): array
    {
        $rfids = collect($request->input('rfid'))->filter()->unique()->values()->all();

        return [
            'rfids' => $rfids,
            'key' => $request->input('key'),
            'rsScan' => (int) $request->input('rs_id'),
            'now' => now()->format('Y-m-d H:i:s'),
            'userId' => auth()->id() ?? $request->user()?->id,
            'details' => DetailLinen::whereIn('detail_rfid', $rfids)->get()->keyBy('detail_rfid'),
            'outstandingExisting' => Outstanding::whereIn('outstanding_rfid', $rfids)->get()->keyBy('outstanding_rfid'),
        ];
    }

    // =========================================================================
    // 4) BUILD — transaksi + outstanding rows
    // =========================================================================

    private function buildRows(array $ctx, string $statusTransaksi, string $statusProcess): array
    {
        $transaksi = [];
        $outstanding = [];
        $toKotor = [];

        foreach ($ctx['rfids'] as $rfid) {
            $detail = $ctx['details'][$rfid] ?? null;
            $out = $ctx['outstandingExisting'][$rfid] ?? null;

            if ($detail) {
                $this->buildForRegistered($rfid, $detail, $out, $ctx, $statusTransaksi, $statusProcess, $transaksi, $outstanding, $toKotor);
            } else {
                $this->buildForUnregistered($rfid, $out, $ctx, $statusTransaksi, $statusProcess, $transaksi, $outstanding);
            }
        }

        $transaksi = collect($transaksi)->unique('transaksi_rfid')->values()->all();

        return compact('transaksi', 'outstanding', 'toKotor');
    }

    private function buildForRegistered(string $rfid, $detail, $out, array $ctx, string $statusTransaksi, string $statusProcess, array &$transaksi, array &$outstanding, array &$toKotor): void
    {
        $bedaRs = $ctx['rsScan'] == $detail->detail_id_rs ? 'TIDAK' : 'YA';

        if (! $this->existsToday($rfid)) {
            $transaksi[] = [
                'transaksi_key' => $ctx['key'],
                'transaksi_rfid' => $rfid,
                'transaksi_rs_ori' => $detail->detail_id_rs,
                'transaksi_rs_scan' => $ctx['rsScan'],
                'transaksi_beda_rs' => $bedaRs,
                'transaksi_id_ruangan' => $detail->detail_id_ruangan,
                'transaksi_status' => $statusTransaksi,
                'transaksi_created_at' => $ctx['now'],
                'transaksi_created_by' => $ctx['userId'],
                'transaksi_updated_at' => $ctx['now'],
                'transaksi_updated_by' => $ctx['userId'],
            ];
        }

        if (! $out) {
            $outstanding[] = [
                'outstanding_rfid' => $rfid,
                'outstanding_key' => $ctx['key'],
                'outstanding_rs_ori' => $detail->detail_id_rs,
                'outstanding_rs_scan' => $ctx['rsScan'],
                'outstanding_status_beda_rs' => $bedaRs,
                'outstanding_id_ruangan' => $detail->detail_id_ruangan,
                'outstanding_status_transaksi' => $statusTransaksi,
                'outstanding_status_proses' => $statusProcess,
                'outstanding_status_hilang' => 'NORMAL',
                'outstanding_created_at' => $ctx['now'],
                'outstanding_updated_at' => $ctx['now'],
                'outstanding_created_by' => $ctx['userId'],
                'outstanding_updated_by' => $ctx['userId'],
            ];
        }

        if ($statusTransaksi === TransactionType::KOTOR) {
            $toKotor[] = $rfid;
        }
    }

    private function buildForUnregistered(string $rfid, $out, array $ctx, string $statusTransaksi, string $statusProcess, array &$transaksi, array &$outstanding): void
    {
        if (! $this->existsToday($rfid)) {
            $transaksi[] = [
                'transaksi_key' => $ctx['key'],
                'transaksi_rfid' => $rfid,
                'transaksi_rs_ori' => null,
                'transaksi_rs_scan' => $ctx['rsScan'],
                'transaksi_beda_rs' => 'BELUM_REGISTER',
                'transaksi_id_ruangan' => null,
                'transaksi_status' => $statusTransaksi,
                'transaksi_created_at' => $ctx['now'],
                'transaksi_created_by' => $ctx['userId'],
                'transaksi_updated_at' => $ctx['now'],
                'transaksi_updated_by' => $ctx['userId'],
            ];
        }

        if (! $out) {
            $outstanding[] = [
                'outstanding_rfid' => $rfid,
                'outstanding_key' => $ctx['key'],
                'outstanding_rs_ori' => null,
                'outstanding_rs_scan' => $ctx['rsScan'],
                'outstanding_status_beda_rs' => 'YA',
                'outstanding_id_ruangan' => null,
                'outstanding_status_transaksi' => $statusTransaksi,
                'outstanding_status_proses' => $statusProcess,
                'outstanding_status_hilang' => 'NORMAL',
                'outstanding_created_at' => $ctx['now'],
                'outstanding_updated_at' => $ctx['now'],
                'outstanding_created_by' => $ctx['userId'],
                'outstanding_updated_by' => $ctx['userId'],
            ];
        }
    }

    private function existsToday(string $rfid): bool
    {
        return Transaksi::where('transaksi_rfid', $rfid)->whereDate('transaksi_created_at', today())->exists();
    }

    // =========================================================================
    // 5) PERSIST — chunk insert + upsert outstanding
    // =========================================================================

    private function persistTransactions(array $transaksi): void
    {
        if (empty($transaksi)) {
            return;
        }
        foreach (array_chunk($transaksi, 500) as $chunk) {
            Transaksi::insert($chunk);
        }
    }

    private function persistOutstanding(array $outstanding, $existingMap, string $now, $userId): void
    {
        $inserts = [];
        $updates = [];
        foreach ($outstanding as $row) {
            isset($existingMap[$row['outstanding_rfid']]) ? $updates[] = $row : $inserts[] = $row;
        }

        if (! empty($inserts)) {
            foreach (array_chunk($inserts, 500) as $chunk) {
                Outstanding::insert($chunk);
            }
        }

        foreach ($updates as $row) {
            Outstanding::where('outstanding_rfid', $row['outstanding_rfid'])->update([
                'outstanding_key' => $row['outstanding_key'],
                'outstanding_rs_scan' => $row['outstanding_rs_scan'],
                'outstanding_status_beda_rs' => $row['outstanding_status_beda_rs'],
                'outstanding_id_ruangan' => $row['outstanding_id_ruangan'],
                'outstanding_status_transaksi' => $row['outstanding_status_transaksi'],
                'outstanding_status_proses' => $row['outstanding_status_proses'],
                'outstanding_updated_at' => $now,
                'outstanding_updated_by' => $userId,
            ]);
        }
    }

    private function refreshExistingOutstanding(array $ctx, string $statusTransaksi, string $statusProcess): void
    {
        $outstandingUpdates = collect($ctx['outstandingExisting'])->keys();
        foreach ($ctx['rfids'] as $rfid) {
            if (! isset($ctx['outstandingExisting'][$rfid])) {
                continue;
            }
            // sudah di-handle di persistOutstanding jika masuk $outstanding array — skip agar tidak double update
            $wasUpdated = false;
            // cek apakah rfid ini sudah di-update via persistOutstanding: ada di outstandingExisting dan tidak ada di inserts
            // Sederhananya: selalu refresh status ke transaksi terbaru agar stock = transaksi terakhir
            $detail = $ctx['details'][$rfid] ?? null;
            $bedaRs = $detail ? ($ctx['rsScan'] == $detail->detail_id_rs ? 'TIDAK' : 'YA') : 'BELUM_REGISTER';
            // hanya refresh jika belum di-update barusan (tidak ada di outstanding yang baru)
            // kita update idempoten — aman di-overwrite
            Outstanding::where('outstanding_rfid', $rfid)->update([
                'outstanding_status_transaksi' => $statusTransaksi,
                'outstanding_status_proses' => $statusProcess,
                'outstanding_updated_at' => $ctx['now'],
                'outstanding_updated_by' => $ctx['userId'],
                'outstanding_status_beda_rs' => $bedaRs,
            ]);
        }
    }

    private function markDetailsAsKotor(array $toKotor, string $now): void
    {
        if (empty($toKotor)) {
            return;
        }
        DetailLinen::whereIn('detail_rfid', array_unique($toKotor))->update([
            'detail_updated_at' => $now,
            'detail_status_linen' => 'KOTOR',
        ]);
    }

    // =========================================================================
    // 6) RESPONSE — audit + json
    // =========================================================================

    private function logTransactions(array $transaksi, Request $request): void
    {
        if (empty($transaksi)) {
            return;
        }
        foreach ($transaksi as $row) {
            activity('transaksi')
                ->causedBy(auth()->user() ?? $request->user())
                ->withProperties([
                    'key' => $row['transaksi_key'],
                    'rfid' => $row['transaksi_rfid'],
                    'status' => $row['transaksi_status'],
                    'rs_scan' => $row['transaksi_rs_scan'],
                    'rs_ori' => $row['transaksi_rs_ori'],
                ])
                ->log('Transaksi '.$row['transaksi_status'].' RFID '.$row['transaksi_rfid']);
        }
    }

    private function successResponse(array $ctx, string $statusTransaksi, array $transaksi)
    {
        return Notes::sentJson([
            'status' => true,
            'code' => 200,
            'name' => Notes::create,
            'message' => 'Transaksi berhasil.',
            'data' => [
                'key' => $ctx['key'],
                'status' => $statusTransaksi,
                'rfid_count' => count($ctx['rfids']),
                'inserted' => count($transaksi),
            ],
        ]);
    }
}
