<?php

namespace App\Http\Controllers\Api;

use App\Enums\LinenStatusEnum;
use App\Enums\LogType;
use App\Enums\RsStatusEnum;
use App\Enums\TransactionType;
use App\Http\Controllers\Controller;
use App\Models\DetailLinen;
use App\Models\Outstanding;
use App\Models\Transaksi;
use App\Models\TransaksiMaster;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Plugins\Notes;
use Throwable;

/**
 * Grouping QC per RFID — adopsi closure legacy di routes/api.php.
 *
 * Kontrak desktop (GroupingDAO): response OBJEK MENTAH tanpa envelope Notes
 * (rfid, linen_id/linen_nama = jenis linen, status_*, tanggal_*, user_nama,
 * status_linen) + 1 baris activity GROUPING per scan bila RFID ketemu.
 *
 * Aturan main:
 * - Grouping = QC lolos = linen masuk gudang utama (GUDANG), simpan/refresh
 *   baris outstanding; tandai baris transaksi lahir-grouping (key prefix GRP)
 *   yang belum di-grouping (transaksi_grouping = 'YA',
 *   transaksi_grouping_date = hari ini). Baris sync kotor (flag 'TIDAK')
 *   tidak ikut diflag.
 * - Asumsi operasional: grouping = linen kotor tiba di gudang (mungkin tak
 *   terscan di RS). Selain linen fresh-register, selalu pastikan ada baris
 *   transaksi KOTOR hari ini — buat baru dengan key prefix GRP bila belum
 *   ada (idempoten per hari, anti dobel-scan).
 * - Outstanding (baru maupun sudah ada, termasuk REGISTER) →
 *   rs_ori, rs_scan & ruangan selalu null KECUALI linen DEDICATED
 *   (detail_status_kepemilikan / rs_status DEDICATED): DEDICATED tetap
 *   simpan detail_id_rs/detail_id_ruangan agar tidak hilang.
 *   FREE/GROUP tetap null (gudang pusat), warehouse tetap terisi.
 *   Asal RS tercatat di baris transaksi.
 * - RFID tak dikenal → Notes::error (bukan 404 mentah).
 */
class GroupingController extends Controller
{
    /** Cache hasil Schema::hasTable per request (hindari hit information_schema berulang). */
    private static array $tableCache = [];

    public function show(string $rfid)
    {
        $rfid = trim($rfid);

        if ($rfid === '') {
            return Notes::error(null, 'RFID tidak boleh kosong');
        }

        try {
            // ponytail: DB::transaction = commit/rollback otomatis — gagal di
            // tengah (termasuk log activity) me-rollback semuanya, tanpa
            // begin/commit manual yang rawan lupa rollback di cabang baru.
            $resource = DB::transaction(fn () => $this->process($rfid));
        } catch (ModelNotFoundException $e) {
            return Notes::error($rfid, 'RFID '.$rfid.' tidak ditemukan');
        } catch (Throwable $e) {
            report($e);
            if ((int) $e->getCode() === 23000) {
                if (config('app.debug')) {
                    $message = explode('for key', $e->getMessage());
                    $clean = str_replace('SQLSTATE[23000]: Integrity constraint violation: 1062', 'RFID', $message[0] ?? $e->getMessage());

                    return Notes::error($clean);
                }

                return Notes::error($rfid, 'RFID sudah terdaftar.');
            }

            return Notes::error($rfid, config('app.debug') ? $e->getMessage() : 'Terjadi kesalahan pada server.');
        }

        // Desktop GroupingDAO expects raw object (bukan envelope Notes).
        return response()->json($resource);
    }

    /**
     * Bentuk alternatif RFID untuk lookup (aturan sama dengan desktop
     * R700Service::CanonicalEpc): hex genap yang byte-nya printable ASCII
     * <-> teksnya, dua arah karena DB campuran (register handheld = hex,
     * register R700 = teks). Maksimal 2 kandidat, tanpa query bila tak ada.
     *
     * @return string[]
     */
    private static function alternateRfids(string $rfid): array
    {
        $alts = [];

        if (strlen($rfid) > 0 && strlen($rfid) % 2 === 0 && preg_match('/\A[0-9A-Fa-f]+\z/', $rfid)) {
            $bin = @hex2bin($rfid);
            if (is_string($bin) && $bin !== '' && preg_match('/\A[\x20-\x7E]+\z/', $bin)) {
                $alts[] = strtoupper($bin);
            }
        }

        if (preg_match('/\A[\x20-\x7E]+\z/', $rfid)) {
            $hex = strtoupper(bin2hex($rfid));
            if ($hex !== $rfid) {
                $alts[] = $hex;
            }
        }

        return array_values(array_unique($alts));
    }

    /**
     * Inti transaksional: differential update — hanya tabel yang berubah
     * yang ditulis (1× update outstanding ATAU 1× insert + opsional transaksi).
     *
     * @return array<string,mixed> resource mentah untuk desktop
     */
    private function process(string $rfid): array
    {
        $user = auth()->user();
        $userId = $user->id;
        $userName = $user->name ?? (string) $userId;
        $now = now();
        $date = $now->format('Y-m-d H:i:s');
        $today = $now->format('Y-m-d');

        // BKA: DetailLinen primary = detail_rfid, bukan id. Satu query
        // dengan semua relasi yang dipakai response (tanpa N+1).
        // Format ganda: handheld RWDev kirim hex mentah ("4430..."), R700 +
        // DB pakai teks ASCII ("D000..."). Cari bentuk persis dulu, lalu
        // bentuk alternatif — tulis/balas selalu pakai bentuk tersimpan.
        $detail = DetailLinen::with(['hasRs', 'hasRuangan', 'hasJenis', 'hasBahan', 'hasSupplier'])
            ->where('detail_rfid', $rfid)
            ->first();

        if (! $detail) {
            $alts = self::alternateRfids($rfid);
            if ($alts !== []) {
                $detail = DetailLinen::with(['hasRs', 'hasRuangan', 'hasJenis', 'hasBahan', 'hasSupplier'])
                    ->whereIn('detail_rfid', $alts)
                    ->first();
            }
        }

        if (! $detail) {
            throw new ModelNotFoundException("RFID {$rfid} tidak ditemukan");
        }

        $rfid = $detail->detail_rfid;

        $this->log("Grouping QC RFID {$rfid}", $detail, $rfid);

        $statusLinen = $detail->detail_status_linen;
        // Key prefix GRP = penanda baris lahir dari grouping (bukan scan kotor).
        $key = $this->uniqueKey((string) env('CODE_GROUPING', 'GRP'));

        $outstanding = Outstanding::where('outstanding_rfid', $rfid)->first();

        // ponytail: flag response HANYA terisi bila request ini membuat baris
        // transaksi (KOTOR/REGISTER) — status linen yang sudah ada sebelumnya
        // tidak ikut (legacy: $flag default 'Normal').
        $flag = 'Normal';

        if ($outstanding) {
            $outstandingExisted = true;
            // Sudah PACKING jangan diturunkan lagi ke GUDANG — pertahankan
            // status packing (RS/ruangan/timestamp ikut utuh).
            if (($outstanding->outstanding_status_proses ?? null) !== 'PACKING') {
                $outstanding->update($this->outstandingData($detail, $rfid, $date, $userId));
            }
        } else {
            $outstandingExisted = false;
            [$outstanding, $flag] = $this->storeNewOutstanding($detail, $rfid, $key, $date, $today, $userId, $statusLinen);
        }

        // Pokok: outstanding sudah ada = jangan double — tidak insert baris
        // baru ke transaksi / transaksi_master / outstanding. Cukup refresh
        // outstanding + tandai grouping. Hanya stay baru (tanpa outstanding)
        // yang boleh melahirkan transaksi KOTOR bila bukan fresh-register.
        if (! $outstandingExisted && ! $this->isFreshRegister($detail, $statusLinen, $today)) {
            $flag = $this->ensureGroupingTransaction($detail, $rfid, $key, $date, $today, $userId, $flag);
        }

        // Posisi linen = gudang utama (QC lolos = masuk gudang). Satu-satunya
        // tulis ke detail_linen di flow ini (tanpa dead-store status perantara).
        $detail->update(['detail_status_linen' => LinenStatusEnum::GUDANG]);

        // Tandai transaksi RFID ini yang lahir dari grouping (key prefix GRP)
        // sebagai sudah di-grouping — hanya baris yang masih kosong.
        // Baris sync kotor (flag TIDAK) tidak ikut diflag YA.
        $grpPrefix = (string) env('CODE_GROUPING', 'GRP');
        Transaksi::where('transaksi_rfid', $rfid)
            ->whereNull('transaksi_grouping_date')
            ->where('transaksi_key', 'like', $grpPrefix.'%')
            ->update(['transaksi_grouping_date' => $today, 'transaksi_grouping' => 'YA']);

        $this->touchOpnameScan($rfid, $outstanding->outstanding_key ?? $key, $date);

        return $this->toResource($detail, $outstanding, $rfid, $userName, $flag);
    }

    /**
     * Linen fresh-register = status REGISTER dan belum pernah keluar
     * (report null atau hari ini). Ini satu-satunya kasus grouping yang
     * TIDAK boleh dipaksa jadi transaksi KOTOR — linennya memang belum
     * pernah kotor. Cabang REGISTER lama di storeNewOutstanding tetap jalan.
     */
    private function isFreshRegister(DetailLinen $detail, ?string $statusLinen, string $today): bool
    {
        $isRegister = $statusLinen === LinenStatusEnum::REGISTER || $statusLinen === 'REGISTER';

        if (! $isRegister) {
            return false;
        }

        $report = ! empty($detail->detail_report)
            ? Carbon::parse($detail->detail_report)->format('Y-m-d')
            : null;

        return $report === null || $report === $today;
    }

    /**
     * Pastikan ada baris transaksi KOTOR hari ini untuk RFID ini.
     * Hanya dipanggil untuk stay baru (tanpa outstanding sebelumnya):
     * idempoten per hari, bila sudah ada (mis. dibuat storeNewOutstanding)
     * tidak buat lagi — penandaan grouped dilakukan pemanggil.
     * Kembalikan flag respons.
     *
     * @return string flag status_linen untuk resource desktop
     */
    private function ensureGroupingTransaction(
        DetailLinen $detail,
        string $rfid,
        string $key,
        string $date,
        string $today,
        int $userId,
        string $flag,
    ): string {
        $existsToday = Transaksi::where('transaksi_rfid', $rfid)
            ->whereDate('transaksi_created_at', $today)
            ->exists();

        if ($existsToday) {
            return $flag;
        }

        $this->log("Grouping QC_TRANSACTION RFID {$rfid}", $detail, $rfid);

        Transaksi::create([
            'transaksi_key' => $key,
            'transaksi_rfid' => $rfid,
            'transaksi_rs_ori' => $detail->detail_id_rs,
            'transaksi_rs_scan' => $detail->detail_id_rs,
            'transaksi_beda_rs' => 'TIDAK',
            'transaksi_id_ruangan' => $detail->detail_id_ruangan,
            'transaksi_status' => TransactionType::KOTOR,
            'transaksi_grouping' => 'YA',
            'transaksi_grouping_date' => $today,
            'transaksi_created_at' => $date,
            'transaksi_created_by' => $userId,
            'transaksi_updated_at' => $date,
            'transaksi_updated_by' => $userId,
        ]);

        // ponytail: pemanggil menjamin ini stay baru (tanpa outstanding
        // sebelumnya), jadi master header selalu dibuat untuk key ini.
        $this->persistMaster($key, $detail->detail_id_rs, TransactionType::KOTOR, $today, $date, $userId);
        $this->touchPending($rfid, TransactionType::KOTOR, $date);

        return 'KOTOR';
    }

    /**
     * Auto number CODE + ymd + 4 digit random, retry ≤5x bila duplikat.
     */
    private function uniqueKey(string $prefix): string
    {
        $ymd = date('ymd');
        $key = $prefix.$ymd.str_pad((string) random_int(0, 9999), 4, '0', STR_PAD_LEFT);

        $try = 0;
        while ($try < 5 && Transaksi::where('transaksi_key', $key)->exists()) {
            $key = $prefix.$ymd.str_pad((string) random_int(0, 9999), 4, '0', STR_PAD_LEFT);
            $try++;
        }

        return $key;
    }

    /**
     * Payload outstanding — grouping = linen masuk gudang pusat: RS
     * (ori/scan) + ruangan null untuk FREE/GROUP, tapi DIPERTAHANKAN
     * untuk DEDICATED (detail_status_kepemilikan / rs_status DEDICATED)
     * agar RS + ruangan tidak hilang di outstanding. Warehouse +
     * transaksi tetap menandai asal.
     */
    private function outstandingData(DetailLinen $detail, string $rfid, string $date, int $userId): array
    {
        $isDedicated = $this->isDedicated($detail);

        return [
            'outstanding_rfid' => $rfid,
            'outstanding_status_proses' => 'GUDANG',
            'outstanding_id_warehouse' => (int) env('GUDANG_UTAMA_ID', 1),
            'outstanding_updated_at' => $date,
            'outstanding_updated_by' => $userId,
            'outstanding_rs_ori' => $isDedicated ? $detail->detail_id_rs : null,
            'outstanding_rs_scan' => $isDedicated ? $detail->detail_id_rs : null,
            'outstanding_id_ruangan' => $isDedicated ? $detail->detail_id_ruangan : null,
            'outstanding_status_hilang' => 'NORMAL',
            'outstanding_hilang_created_at' => null,
            'outstanding_pending_created_at' => null,
        ];
    }

    /**
     * RFID dedicated = kepemilikan DEDICATED atau RS-nya bertipe DEDICATED.
     * Keduanya selaras (register 1 RS ikut tipe RS), cek dua-duanya agar
     * data lama yang hanya satu sisinya terisi tetap terbawa.
     */
    private function isDedicated(DetailLinen $detail): bool
    {
        if (($detail->detail_status_kepemilikan ?? null) === RsStatusEnum::DEDICATED) {
            return true;
        }

        try {
            return ($detail->hasRs?->rs_status ?? null) === RsStatusEnum::DEDICATED;
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * Outstanding baru: buat baris transaksi hanya bila ada report lama yang
     * beda hari (REGISTER → status REGISTER, selain itu KOTOR); cegah duplikat
     * transaksi hari yang sama.
     *
     * @return array{0: Outstanding, 1: string} model + flag status_linen response
     */
    private function storeNewOutstanding(
        DetailLinen $detail,
        string $rfid,
        string $key,
        string $date,
        string $today,
        int $userId,
        ?string $statusLinen,
    ): array {
        $isRegister = $statusLinen === LinenStatusEnum::REGISTER || $statusLinen === 'REGISTER';
        $reportDate = ! empty($detail->detail_report) ? Carbon::parse($detail->detail_report)->format('Y-m-d') : null;

        $needsTransaksi = $reportDate !== null && $reportDate !== $today;
        $transaksiStatus = $isRegister ? 'REGISTER' : TransactionType::KOTOR;
        $flag = 'Normal';

        $data = $this->outstandingData($detail, $rfid, $date, $userId);

        if ($needsTransaksi) {
            $existsToday = Transaksi::where('transaksi_rfid', $rfid)
                ->whereDate('transaksi_created_at', $today)
                ->exists();

            if (! $existsToday) {
                $this->log("Grouping QC_TRANSACTION RFID {$rfid}", $detail, $rfid);

                Transaksi::create([
                    'transaksi_key' => $key,
                    'transaksi_rfid' => $rfid,
                    'transaksi_rs_ori' => $detail->detail_id_rs,
                    'transaksi_rs_scan' => $detail->detail_id_rs,
                    'transaksi_beda_rs' => 'TIDAK',
                    'transaksi_id_ruangan' => $detail->detail_id_ruangan,
                    'transaksi_status' => $transaksiStatus,
                    'transaksi_created_at' => $date,
                    'transaksi_created_by' => $userId,
                    'transaksi_updated_at' => $date,
                    'transaksi_updated_by' => $userId,
                ]);

                $this->persistMaster($key, $detail->detail_id_rs, $transaksiStatus, $today, $date, $userId);

                $flag = $isRegister ? 'REGISTER' : 'KOTOR';
            }

            $this->touchPending($rfid, $transaksiStatus, $date);

            return [Outstanding::create($data + [
                'outstanding_key' => $key,
                'outstanding_status_transaksi' => $transaksiStatus,
                'outstanding_created_at' => $date,
                'outstanding_created_by' => $userId,
            ]), $flag];
        }

        // Tanpa transaksi: REGISTER → outstanding REGISTER (created_at ikut
        // detail_created_at), selain itu outstanding QC kosong.
        if ($isRegister) {
            $createdAt = $detail->detail_created_at
                ? Carbon::parse($detail->detail_created_at)->format('Y-m-d H:i:s')
                : $date;

            return [Outstanding::create($data + [
                'outstanding_key' => $key,
                'outstanding_status_transaksi' => 'REGISTER',
                'outstanding_created_at' => $createdAt,
                'outstanding_created_by' => $userId,
            ]), $flag];
        }

        return [Outstanding::create($data + [
            'outstanding_key' => $key,
            // ponytail: outstanding_status_transaksi = ENUM KOTOR/REJECT/REWASH/
            // REGISTER/WAREHOUSE/HUB — status linen lain (mis. GUDANG) dipaksa
            // KOTOR agar tidak 500 truncation, selaras asumsi grouping di atas
            // (= linen kotor tiba di gudang, transaksi KOTOR menyertai).
            'outstanding_status_transaksi' => in_array($statusLinen, [
                TransactionType::KOTOR, TransactionType::REJECT,
                TransactionType::REWASH, 'REGISTER', 'WAREHOUSE', 'HUB',
            ], true) ? $statusLinen : TransactionType::KOTOR,
            'outstanding_created_at' => $date,
            'outstanding_created_by' => $userId,
        ]), $flag];
    }

    /**
     * Header master 1 baris per key grouping (idempoten) supaya muncul di
     * /transaksi/table + report berbasis master. Qty = SUM jenis_berat
     * (berat jenis linen), bukan inputan — grouping tidak menimbang.
     * Null bila jenis tidak punya berat (belum diketahui).
     */
    private function persistMaster(string $key, ?int $rsId, string $status, string $today, string $date, int $userId): void
    {
        try {
            if (! self::hasTable('transaksi_master')) {
                return;
            }

            // ponytail: hitung dari baris transaksi key ini (pola sama dengan
            // beratBersih() di TransaksiApiController) — 1 RFID per key
            // grouping, tapi SUM tetap benar bila key dipakai ulang.
            $qty = (float) DB::table('transaksi')
                ->join('detail_linen', 'detail_linen.detail_rfid', '=', 'transaksi.transaksi_rfid')
                ->join('jenis_linen', 'jenis_linen.jenis_id', '=', 'detail_linen.detail_id_jenis')
                ->where('transaksi.transaksi_key', $key)
                ->sum('jenis_linen.jenis_berat');

            $master = TransaksiMaster::firstOrNew(['master_kode' => $key]);
            $isNew = ! $master->exists;

            $master->master_rs_id = $rsId;
            $master->master_status = $status;
            $master->master_tanggal = $today;
            $master->master_qty = $qty > 0 ? round($qty, 3) : null;
            $master->master_updated_at = $date;
            $master->master_updated_by = $userId;

            if ($isNew) {
                $master->master_created_at = $date;
                $master->master_created_by = $userId;
            }

            $master->save();
        } catch (Throwable $e) {
        }
    }

    /**
     * Pending update legacy — hanya bila tabel ada (cached).
     */
    private function touchPending(string $rfid, string $transaksiStatus, string $date): void
    {
        try {
            if (! self::hasTable('pending')) {
                return;
            }

            DB::table('pending')
                ->where('pending_transaksi', '!=', TransactionType::BERSIH)
                ->where('pending_rfid', $rfid)
                ->update([
                    'pending_updated_at' => $date,
                    'pending_transaksi' => $transaksiStatus,
                    'pending_proses' => 'QC',
                ]);
        } catch (Throwable $e) {
        }
    }

    /**
     * Opname aktif: tandai RFID ini ketemu via QC (kolom tinyint 0|1).
     */
    private function touchOpnameScan(string $rfid, string $key, string $date): void
    {
        try {
            if (! self::hasTable('opname') || ! self::hasTable('opname_detail')) {
                return;
            }

            $opname = DB::table('opname')->where('opname_status', 1)->first();

            if (! $opname) {
                return;
            }

            DB::table('opname_detail')
                ->where('opname_detail_id_opname', $opname->opname_id)
                ->where('opname_detail_rfid', $rfid)
                ->where('opname_detail_ketemu', 0)
                ->update([
                    'opname_detail_scan_rs' => 1,
                    'opname_detail_ketemu' => 1,
                    'opname_detail_waktu' => $date,
                    'opname_detail_sync' => 1,
                    'opname_detail_reff' => $key,
                    'opname_detail_scan_by' => 'QC',
                ]);
        } catch (Throwable $e) {
        }
    }

    private static function hasTable(string $table): bool
    {
        return self::$tableCache[$table] ??= Schema::hasTable($table);
    }

    private function log(string $message, DetailLinen $detail, string $rfid): void
    {
        try {
            activity(LogType::GROUPING)
                ->causedBy(auth()->user())
                ->performedOn($detail)
                ->withProperties(['rfid' => $rfid])
                ->log($message);
        } catch (Throwable $e) {
        }
    }

    /**
     * Resource mentah GroupingDAO desktop — key & tipe string dipertahankan
     * (linen_id/linen_nama = jenis linen, bukan RFID).
     *
     * @return array<string,mixed>
     */
    private function toResource(
        DetailLinen $detail,
        Outstanding $outstanding,
        string $rfid,
        string $userName,
        string $flag,
    ): array {
        // ponytail: pakai model outstanding yang sudah dipegang (tanpa
        // re-query fresh) — created_at/updated_at sudah sinkron di memori.
        $fmt = fn ($v) => $v ? Carbon::parse($v)->format('Y-m-d') : null;

        return [
            'rfid' => $rfid,
            'linen_id' => (string) ($detail->detail_id_jenis ?? ''),
            'linen_nama' => $detail->hasJenis?->jenis_nama ?? '',
            'rs_id' => (string) ($detail->detail_id_rs ?? ''),
            'rs_nama' => $detail->hasRs?->rs_nama ?? '',
            'ruangan_id' => (string) ($detail->detail_id_ruangan ?? ''),
            'ruangan_nama' => $detail->hasRuangan?->ruangan_nama ?? '',
            'status_transaksi' => $outstanding->outstanding_status_transaksi ?? '',
            'status_proses' => $outstanding->outstanding_status_proses ?? 'GUDANG',
            'status_kepemilikan' => $detail->detail_status_kepemilikan ?? null,
            'tanggal_create' => $fmt($outstanding->outstanding_created_at),
            'tanggal_update' => $fmt($outstanding->outstanding_updated_at),
            'user_nama' => $userName,
            'status_linen' => $flag,
        ];
    }
}
