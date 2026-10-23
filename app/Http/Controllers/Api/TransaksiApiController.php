<?php

namespace App\Http\Controllers\Api;

use App\Enums\LogType;
use App\Enums\TransactionType;
use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\DetailLinen;
use App\Models\Outstanding;
use App\Models\Transaksi;
use App\Models\TransaksiMaster;
use App\Models\User;
use App\Support\DashboardCache;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Plugins\Notes;

/**
 * RFID dirty/return/rewash scan — entry from desktop.
 * Flow: validate → normalize rfids → load context → build rows → persist → refresh → response.
 */
class TransaksiApiController extends Controller
{
    // =========================================================================
    // 1) ENTRY — satu method eksplisit per tipe (Route::post kotor/retur/rewash).
    //    transaction() hanya dipanggil internal dari ketiganya.
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
        // ponytail: sync kotor desktop bisa 10rb+ RFID per request.
        set_time_limit(0);

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
            $this->persistMaster($ctx, $statusTransaksi);
            $this->persistOutstanding($rows['outstanding'], $ctx['outstandingExisting'], $ctx['now'], $ctx['userId']);
            $this->refreshExistingOutstanding($ctx, $statusTransaksi, $statusProcess);
            $this->markDetailsAsKotor($rows['toKotor'], $ctx['now']);
            $this->logTransactions($rows['transaksi'], $request, $ctx['details']->all());

            DB::commit();
            DashboardCache::flush();

            return $this->successResponse($ctx, $statusTransaksi, $rows['transaksi']);
        } catch (\Throwable $th) {
            DB::rollBack();
            report($th);

            return Notes::failed(500, config('app.debug') ? $th->getMessage() : 'Terjadi kesalahan pada server.');
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
        // qty = BERAT kotor timbangan (kg, desimal, opsional) — bukan
        // hitung RFID. Berat bersih pembanding (= SUM jenis_berat per RFID)
        // dihitung server, tidak dikirim desktop. Terima koma desimal
        // Indonesia ("10,5") — normalisasi ke titik sebelum rule numeric.
        if (is_string($request->input('qty'))) {
            $request->merge(['qty' => str_replace(',', '.', $request->input('qty'))]);
        }
        $request->validate([
            'rfid' => 'required|array|min:1',
            'rfid.*' => 'required|string',
            'rs_id' => 'required|integer|exists:rs,rs_id',
            'key' => 'required|string|max:255',
            'qty' => 'nullable|numeric|min:0',
        ]);
    }

    private function prepareContext(Request $request): array
    {
        $rfids = collect($request->input('rfid'))->filter()->unique()->values()->all();

        // ponytail: 10rb+ RFID per sync — dilarang query di dalam loop.
        // Semua state yang dibutuhkan buildRows diambil di muka (3 query).
        return [
            'rfids' => $rfids,
            'key' => $request->input('key'),
            'rsScan' => (int) $request->input('rs_id'),
            'totalQty' => $request->input('qty') !== null ? (float) $request->input('qty') : null,
            'now' => now()->format('Y-m-d H:i:s'),
            'userId' => auth()->id() ?? $request->user()?->id,
            'details' => DetailLinen::whereIn('detail_rfid', $rfids)->get()->keyBy('detail_rfid'),
            'outstandingExisting' => Outstanding::whereIn('outstanding_rfid', $rfids)->get()->keyBy('outstanding_rfid'),
            'todayExisting' => Transaksi::whereDate('transaksi_created_at', today())
                ->whereIn('transaksi_rfid', $rfids)
                ->pluck('transaksi_rfid')
                ->flip()
                ->all(),
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

            // andalan (TransaksiController::rfidCanSyncToServer): RFID hanya
            // boleh di-sync kalau linen masih BERSIH dan — untuk KOTOR — sudah
            // lewat TRANSACTION_HOURS_ALLOWED jam sejak detail_updated_at. Yang tidak
            // lolos dilewati (tanpa transaksi/outstanding), sisanya tetap diproses.
            if ($detail && empty($out?->outstanding_status_transaksi)
                && ! $this->rfidCanSyncToServer($statusTransaksi, $detail->detail_status_linen, $detail->detail_updated_at)) {
                continue;
            }

            if ($detail) {
                $this->buildForRegistered($rfid, $detail, $out, $ctx, $statusTransaksi, $statusProcess, $transaksi, $outstanding, $toKotor);
            } else {
                $this->buildForUnregistered($rfid, $out, $ctx, $statusTransaksi, $statusProcess, $transaksi, $outstanding);
            }
        }

        $transaksi = collect($transaksi)->unique('transaksi_rfid')->values()->all();

        return compact('transaksi', 'outstanding', 'toKotor');
    }

    /**
     * Port andalan: linen harus BERSIH; khusus KOTOR harus sudah lewat
     * TRANSACTION_HOURS_ALLOWED jam sejak detail_updated_at (default 15) supaya
     * tidak bisa di-scan kotor di hari/shift yang sama.
     */
    private function rfidCanSyncToServer(string $formTransaksi, ?string $statusLinen, $updatedAt): bool
    {
        if ($statusLinen !== TransactionType::BERSIH) {
            return false;
        }

        if ($formTransaksi === TransactionType::KOTOR) {
            if (empty($updatedAt)) {
                return false;
            }

            $hours = (int) env('TRANSACTION_HOURS_ALLOWED', 15);

            return now()->diffInHours(Carbon::parse($updatedAt), true) >= $hours;
        }

        return true;
    }

    private function buildForRegistered(string $rfid, $detail, $out, array $ctx, string $statusTransaksi, string $statusProcess, array &$transaksi, array &$outstanding, array &$toKotor): void
    {
        $bedaRs = $ctx['rsScan'] == $detail->detail_id_rs ? 'TIDAK' : 'YA';

        if (! isset($ctx['todayExisting'][$rfid])) {
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
        if (! isset($ctx['todayExisting'][$rfid])) {
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

    /**
     * Master header 1 baris per key (idempoten: retry key sama = update,
     * bukan duplikat). Berat bersih TIDAK disimpan — dihitung live dari
     * detail dan dikembalikan pemanggil untuk respons.
     * Dilewati bila tabel belum dimigrasi agar API lama tetap jalan.
     *
     * @return float berat bersih (kg) = SUM jenis_berat batch ini
     */
    private function persistMaster(array $ctx, string $statusTransaksi): float
    {
        $bersih = $this->beratBersih($ctx['rfids']);

        if (! Schema::hasTable('transaksi_master')) {
            return $bersih;
        }

        $master = TransaksiMaster::firstOrNew(['master_kode' => $ctx['key']]);
        $isNew = ! $master->exists;

        $master->master_rs_id = $ctx['rsScan'];
        $master->master_status = $statusTransaksi;
        $master->master_tanggal = substr($ctx['now'], 0, 10);
        $master->master_qty = $ctx['totalQty'];
        $master->master_updated_at = $ctx['now'];
        $master->master_updated_by = $ctx['userId'];

        if ($isNew) {
            $master->master_created_at = $ctx['now'];
            $master->master_created_by = $ctx['userId'];
        }

        $master->save();

        return $bersih;
    }

    /**
     * Berat bersih pembanding (kg) = SUM jenis_berat untuk RFID terdaftar
     * dalam batch. RFID tak terregister tidak ikut (berat tak diketahui).
     */
    private function beratBersih(array $rfids): float
    {
        if ($rfids === []) {
            return 0.0;
        }

        return (float) DetailLinen::whereIn('detail_rfid', $rfids)
            ->join('jenis_linen', 'jenis_linen.jenis_id', '=', 'detail_linen.detail_id_jenis')
            ->sum('jenis_linen.jenis_berat');
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
        // ponytail: 10rb+ sync — N update per-RFID diganti bulk per grup
        // bedaRs (maks 3 grup: YA/TIDAK/BELUM_REGISTER). Idempoten, aman
        // di-overwrite seperti sebelumnya.
        $grouped = [];
        foreach ($ctx['rfids'] as $rfid) {
            if (! isset($ctx['outstandingExisting'][$rfid])) {
                continue;
            }
            $detail = $ctx['details'][$rfid] ?? null;
            $bedaRs = $detail ? ($ctx['rsScan'] == $detail->detail_id_rs ? 'TIDAK' : 'YA') : 'BELUM_REGISTER';
            $grouped[$bedaRs][] = $rfid;
        }

        foreach ($grouped as $bedaRs => $rfids) {
            foreach (array_chunk(array_unique($rfids), 2000) as $chunk) {
                Outstanding::whereIn('outstanding_rfid', $chunk)->update([
                    'outstanding_status_transaksi' => $statusTransaksi,
                    'outstanding_status_proses' => $statusProcess,
                    'outstanding_updated_at' => $ctx['now'],
                    'outstanding_updated_by' => $ctx['userId'],
                    'outstanding_status_beda_rs' => $bedaRs,
                ]);
            }
        }
    }

    private function markDetailsAsKotor(array $toKotor, string $now): void
    {
        if (empty($toKotor)) {
            return;
        }
        foreach (array_chunk(array_unique($toKotor), 2000) as $chunk) {
            DetailLinen::whereIn('detail_rfid', $chunk)->update([
                'detail_updated_at' => $now,
                'detail_status_linen' => 'KOTOR',
            ]);
        }
    }

    // =========================================================================
    // 6) RESPONSE — audit + json
    // =========================================================================

    private function logTransactions(array $transaksi, Request $request, array $details = []): void
    {
        if (empty($transaksi)) {
            return;
        }

        // ponytail: 10rb+ sync — N insert activity diganti bulk insert
        // chunk 500 (kolom persis tiruan ActivityLogger manual: event null,
        // attribute_changes '[]', causer dari auth). Subject = DetailLinen
        // RFID baris tersebut.
        $causer = auth()->user() ?? $request->user();
        $now = now()->format('Y-m-d H:i:s');
        $rows = [];

        foreach ($transaksi as $row) {
            // ponytail: log_name = tipe operasi (LogType) agar sekali lihat
            // mencerminkan event-nya, bukan 'transaksi' generik.
            $logType = match ($row['transaksi_status']) {
                TransactionType::KOTOR => LogType::KOTOR,
                TransactionType::REJECT => LogType::RETUR,
                TransactionType::REWASH => LogType::REWASH,
                default => $row['transaksi_status'],
            };
            $subject = $details[$row['transaksi_rfid']] ?? null;
            $rows[] = [
                'log_name' => $logType,
                'description' => 'Transaksi '.$row['transaksi_status'].' RFID '.$row['transaksi_rfid'],
                'event' => null,
                'subject_type' => $subject ? DetailLinen::class : null,
                'subject_id' => $subject ? $row['transaksi_rfid'] : null,
                'causer_type' => $causer ? $causer::class : null,
                'causer_id' => $causer?->getKey(),
                'attribute_changes' => '[]',
                'properties' => json_encode([
                    'key' => $row['transaksi_key'],
                    'rfid' => $row['transaksi_rfid'],
                    'status' => $row['transaksi_status'],
                    'rs_scan' => $row['transaksi_rs_scan'],
                    'rs_ori' => $row['transaksi_rs_ori'],
                ]),
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        // ponytail: bulk insert tidak lewat model event, jadi ditulis manual
        // di sini — satu baris per RFID, tanpa dedupe.
        foreach (array_chunk($rows, 500) as $chunk) {
            Activity::insert($chunk);
        }
    }

    private function successResponse(array $ctx, string $statusTransaksi, array $transaksi)
    {
        // Kontrak persis backup/andalan: envelope Notes::create —
        // {status:true, code:200, name:'Create', message:'Data berhasil di buat',
        //  qty:<echo input>, data:[{key, status_sync, status_transaksi,
        //  status_process, rfid, tanggal_update}]}.
        return Notes::sentJson([
            'status' => true,
            'code' => 200,
            'name' => Notes::create,
            'message' => 'Data berhasil di buat',
            'data' => $this->buildSyncItems($ctx, $statusTransaksi, $transaksi),
        ], 200, ['qty' => $ctx['totalQty']]);
    }

    /**
     * Rincian sinkronisasi per RFID ala backup TransaksiController::transaction:
     * - Punya detail/outstanding sebelumnya: sync=1 bila baris transaksi
     *   dibuat request ini, selain itu sync=0 (pakai status outstanding).
     * - Tanpa detail & outstanding (RFID asing baru): sync=2 + UNKNOWN —
     *   sama seperti backup meski barisnya ikut dibuat request ini.
     */
    private function buildSyncItems(array $ctx, string $statusTransaksi, array $transaksi): array
    {
        $inserted = collect($transaksi)->pluck('transaksi_rfid')->flip()->all();
        $items = [];
        foreach ($ctx['rfids'] as $rfid) {
            $out = $ctx['outstandingExisting'][$rfid] ?? null;
            $detail = $ctx['details'][$rfid] ?? null;
            if ($detail || $out) {
                if (isset($inserted[$rfid])) {
                    $items[] = [
                        'key' => $ctx['key'],
                        'status_sync' => 1,
                        'status_transaksi' => $statusTransaksi,
                        'status_process' => 'SCAN',
                        'rfid' => $rfid,
                        'tanggal_update' => $ctx['now'],
                    ];
                } else {
                    $items[] = [
                        'key' => $ctx['key'],
                        'status_sync' => 0,
                        'status_transaksi' => $out ? $out->outstanding_status_transaksi : null,
                        'status_process' => $out ? $out->outstanding_status_proses : null,
                        'rfid' => $rfid,
                        'tanggal_update' => $detail && $detail->detail_updated_at
                            ? Carbon::parse($detail->detail_updated_at)->format('Y-m-d H:i:s')
                            : $ctx['now'],
                    ];
                }
            } else {
                $items[] = [
                    'key' => $ctx['key'],
                    'status_sync' => 2,
                    'status_transaksi' => 'UNKNOWN',
                    'status_process' => 'UNKNOWN',
                    'rfid' => $rfid,
                    'tanggal_update' => $ctx['now'],
                ];
            }
        }

        return $items;
    }
}
