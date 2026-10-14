<?php

namespace App\Http\Controllers\Api;

use App\Enums\GantiStatusEnum;
use App\Enums\LogType;
use App\Enums\TransactionType;
use App\Http\Controllers\Controller;
use App\Models\DetailLinen;
use App\Models\GantiChip;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Plugins\Notes;
use Throwable;

/**
 * Ganti Pending — ganti chip RFID linen (tab desktop "Ganti Pending").
 *
 * POST /api/ganti-pending (Bearer) { rfid_lama, rfid_baru }
 *
 * Aturan:
 * - Kedua RFID wajib terdaftar di detail_linen.
 * - RS (detail_id_rs) dan Jenis Linen (detail_id_jenis) keduanya harus SAMA,
 *   selain itu tolak dengan pesan error (desktop menampilkannya, form tetap).
 * - Bila sama: RFID diganti dari lama ke baru di tabel outstanding
 *   (1 baris, PK outstanding_rfid) dan transaksi (semua baris history),
 *   dalam satu DB::transaction.
 * - Outstanding TIDAK ada delete: baris LAMA diparkir (warehouse=1),
 *   baris BARU diaktifkan (warehouse=0): status_transaksi + key +
 *   status_proses ikut lama (mis. lama PACKING → baru ikut PACKING).
 *   Bila baru belum punya baris, dibuatkan dari replika data lama.
 * - Bersih: hanya baris yang BELUM ada kode delivery yang ikut chip baru;
 *   yang sudah delivery dipertahankan sebagai histori.
 * - RFID lama WAJIB sudah ada di outstanding (= sudah ada transaksi kotor),
 *   selain itu Simpan digagalkan dengan pesan validasi.
 * - Rename dirambatkan ke tabel anak yang sama seperti cascade web
 *   (DetailLinenController::cascadeRfid): config_linen, transaksi,
 *   outstanding, bersih, opname_detail + baris histori ganti_chip.
 */
class GantiPendingController extends Controller
{
    public function __invoke(Request $request)
    {
        $request->validate([
            'rfid_lama' => 'required|string',
            'rfid_baru' => 'required|string',
            'ganti_status' => 'required|string|in:'.implode(',', GantiStatusEnum::getValues()),
        ]);

        $lama = trim((string) $request->input('rfid_lama'));
        $baru = trim((string) $request->input('rfid_baru'));

        if ($lama === '' || $baru === '') {
            return Notes::validation('RFID lama dan RFID pengganti wajib diisi.', [
                'rfid_lama' => ['RFID lama wajib diisi.'],
                'rfid_baru' => ['RFID pengganti wajib diisi.'],
            ]);
        }

        if ($lama === $baru) {
            return Notes::validation('RFID lama dan RFID pengganti sama.', [
                'rfid_baru' => ['RFID pengganti sama dengan RFID lama.'],
            ]);
        }

        $old = $this->findDetail($lama);
        if ($old === null) {
            return Notes::error($lama, 'RFID lama '.$lama.' tidak ditemukan di detail_linen.');
        }

        $new = $this->findDetail($baru);
        if ($new === null) {
            return Notes::error($baru, 'RFID pengganti '.$baru.' tidak ditemukan di detail_linen.');
        }

        if ((int) $old->detail_id_rs !== (int) $new->detail_id_rs) {
            return Notes::validation(
                'RS tidak sama — lama: '.$this->rsNama($old).', baru: '.$this->rsNama($new).'.',
                ['rfid_baru' => ['RS RFID pengganti ('.$this->rsNama($new).') tidak sama dengan RS RFID lama ('.$this->rsNama($old).').']]
            );
        }

        if ((int) $old->detail_id_jenis !== (int) $new->detail_id_jenis) {
            return Notes::validation(
                'Jenis linen tidak sama — lama: '.$this->jenisNama($old).', baru: '.$this->jenisNama($new).'.',
                ['rfid_baru' => ['Jenis linen RFID pengganti ('.$this->jenisNama($new).') tidak sama dengan RFID lama ('.$this->jenisNama($old).').']]
            );
        }

        // RFID lama wajib sudah ada di outstanding (= sudah ada transaksi kotor).
        if (! DB::table('outstanding')->where('outstanding_rfid', $lama)->exists()) {
            return Notes::validation(
                'RFID lama '.$lama.' belum ada transaksi kotor (tidak ditemukan di outstanding).',
                ['rfid_lama' => ['RFID lama belum ada transaksi kotor (tidak ditemukan di outstanding).']]
            );
        }

        $userId = auth()->id();
        $now = now()->format('Y-m-d H:i:s');
        $gantiStatus = $request->input('ganti_status');

        $result = DB::transaction(function () use ($lama, $baru, $old, $gantiStatus, $userId, $now) {
            // Outstanding: TANPA delete. Baris lama diparkir (warehouse=1),
            // baris baru diaktifkan (warehouse=0) membawa data lanjutan lama —
            // chip baru menggantikan lama untuk diproses lebih lanjut.
            $lamaRow = (array) DB::table('outstanding')->where('outstanding_rfid', $lama)->first();

            $parkirLama = DB::table('outstanding')
                ->where('outstanding_rfid', $lama)
                ->update([
                    'outstanding_id_warehouse' => 1,
                    'outstanding_updated_at' => $now,
                    'outstanding_updated_by' => $userId,
                ]);

            // Replika data lama untuk baris baru (kecuali RFID/warehouse/waktu):
            // status_transaksi + key + status_proses ikut M1
            // (mis. M1 PACKING → M2 ikut PACKING).
            $dataBaru = $lamaRow;
            $dataBaru['outstanding_rfid'] = $baru;
            $dataBaru['outstanding_id_warehouse'] = 0;
            $dataBaru['outstanding_updated_at'] = $now;
            $dataBaru['outstanding_updated_by'] = $userId;

            $baruAda = DB::table('outstanding')->where('outstanding_rfid', $baru)->exists();
            if ($baruAda) {
                $dataUpdate = $dataBaru;
                unset($dataUpdate['outstanding_created_at'], $dataUpdate['outstanding_created_by']);
                $outstandingUpdated = DB::table('outstanding')
                    ->where('outstanding_rfid', $baru)
                    ->update($dataUpdate);
                $outstandingDibuat = false;
            } else {
                $dataBaru['outstanding_created_at'] = $now;
                $dataBaru['outstanding_created_by'] = $userId;
                DB::table('outstanding')->insert($dataBaru);
                $outstandingUpdated = 1;
                $outstandingDibuat = true;
            }

            // Tabel anak lain — rename lama -> baru (tiruan cascadeRfid web
            // supaya tidak ada referensi yatim ke RFID lama).
            // config_linen PK = (rs_id, detail_rfid): hapus dulu baris baru
            // yang bertabrakan, baru rename (baru digantikan data lama).
            $configUpdated = 0;
            $configDihapus = 0;
            if (Schema::hasTable('config_linen')) {
                $lamaRs = DB::table('config_linen')->where('detail_rfid', $lama)->pluck('rs_id');
                if ($lamaRs->isNotEmpty()) {
                    $configDihapus = DB::table('config_linen')
                        ->where('detail_rfid', $baru)
                        ->whereIn('rs_id', $lamaRs)
                        ->delete();
                    $configUpdated = DB::table('config_linen')
                        ->where('detail_rfid', $lama)
                        ->update(['detail_rfid' => $baru]);
                }
            }

            // Transaksi: history, tanpa konflik PK — semua baris lama ikut baru.
            $transaksiUpdated = DB::table('transaksi')
                ->where('transaksi_rfid', $lama)
                ->update(['transaksi_rfid' => $baru]);

            // Bersih: hanya yang BELUM ada kode delivery yang ikut chip baru;
            // yang sudah delivery = histori final, RFID-nya dipertahankan.
            $bersihUpdated = Schema::hasTable('bersih')
                ? DB::table('bersih')->where('bersih_rfid', $lama)->whereNull('bersih_delivery')->update(['bersih_rfid' => $baru])
                : 0;

            $opnameUpdated = Schema::hasTable('opname_detail')
                ? DB::table('opname_detail')->where('opname_detail_rfid', $lama)->update(['opname_detail_rfid' => $baru])
                : 0;

            // Baris transaksi penggantian: rfid = BARU, ganti_rfid = LAMA,
            // ganti_status = pilihan combo_box_pending_status. Linen dianggap
            // masuk kembali sebagai KOTOR (seperti register GANTI_CHIP).
            $ganti = Transaksi::create([
                'transaksi_key' => $this->uniqueKey((string) env('CODE_GANTI', 'GNT')),
                'transaksi_rfid' => $baru,
                'transaksi_ganti_rfid' => $lama,
                'transaksi_ganti_status' => $gantiStatus,
                'transaksi_rs_ori' => $old->detail_id_rs,
                'transaksi_rs_scan' => $old->detail_id_rs,
                'transaksi_beda_rs' => 'TIDAK',
                'transaksi_id_ruangan' => $old->detail_id_ruangan,
                'transaksi_status' => TransactionType::KOTOR,
                'transaksi_created_at' => $now,
                'transaksi_created_by' => $userId,
                'transaksi_updated_at' => $now,
                'transaksi_updated_by' => $userId,
            ]);

            // Histori rantai A→B (dipakai ReportPenggantianLinen + forRfid).
            GantiChip::create([
                'ganti_rfid_lama' => $lama,
                'ganti_rfid_baru' => $baru,
                'ganti_tanggal' => now(),
                'ganti_by' => $userId,
            ]);

            $this->log($lama, $baru, $outstandingUpdated, $transaksiUpdated);

            return [
                'rfid_lama' => $lama,
                'rfid_baru' => $baru,
                'ganti_status' => $gantiStatus,
                'ganti_transaksi_id' => $ganti->transaksi_id,
                'ganti_transaksi_key' => $ganti->transaksi_key,
                'outstanding_lama_parkir' => $parkirLama,
                'outstanding_updated' => $outstandingUpdated,
                'outstanding_baru_dibuat' => $outstandingDibuat,
                'transaksi_updated' => $transaksiUpdated,
                'config_updated' => $configUpdated,
                'config_dihapus' => $configDihapus,
                'bersih_updated' => $bersihUpdated,
                'opname_updated' => $opnameUpdated,
            ];
        });

        return Notes::update($result);
    }

    private function findDetail(string $rfid): ?object
    {
        return DB::table('detail_linen')
            ->leftJoin('rs', 'rs.rs_id', '=', 'detail_linen.detail_id_rs')
            ->leftJoin('jenis_linen', 'jenis_linen.jenis_id', '=', 'detail_linen.detail_id_jenis')
            ->where('detail_linen.detail_rfid', $rfid)
            ->select(['detail_linen.detail_rfid', 'detail_linen.detail_id_rs', 'detail_linen.detail_id_ruangan', 'detail_linen.detail_id_jenis', 'rs.rs_nama', 'jenis_linen.jenis_nama'])
            ->first();
    }

    /**
     * Nomor batch unik prefix GNT (tiruan uniqueKey GroupingController).
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

    private function rsNama(object $row): string
    {
        return $row->rs_nama ?? ('RS id '.$row->detail_id_rs);
    }

    private function jenisNama(object $row): string
    {
        return $row->jenis_nama ?? ('jenis id '.$row->detail_id_jenis);
    }

    private function log(string $lama, string $baru, int $outstandingUpdated, int $transaksiUpdated): void
    {
        try {
            $detail = DetailLinen::where('detail_rfid', $baru)->first();

            $pending = activity(LogType::GANTI_LINEN)->causedBy(auth()->user());
            if ($detail) {
                $pending = $pending->performedOn($detail);
            }

            $pending->withProperties([
                'rfid_lama' => $lama,
                'rfid_baru' => $baru,
                'outstanding_updated' => $outstandingUpdated,
                'transaksi_updated' => $transaksiUpdated,
            ])->log("Ganti pending RFID {$lama} -> {$baru}");
        } catch (Throwable $e) {
        }
    }
}
