<?php

namespace App\Actions;

use App\Concerns\PayloadTrait;
use App\Enums\LinenStatusEnum;
use App\Enums\RegisterEnum;
use App\Enums\RsStatusEnum;
use App\Models\ConfigLinen;
use App\Models\DetailLinen;
use App\Models\Rs;
use App\Models\Ruangan;
use App\Support\DashboardCache;
use Illuminate\Database\QueryException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Lorisleiva\Actions\Concerns\AsAction;

/**
 * Register linen massal (scan masuk).
 *
 * Mengikuti alur register di project lama (andalan/routes/api.php → POST register):
 *   - menerima banyak RFID dalam satu request,
 *   - satu transaksi database (semua sukses atau semua batal),
 *   - menolak RFID yang sudah pernah terdaftar,
 *   - mengisi master kepemilikan (config_linen) bersamaan dengan data linen
 *     (detail_linen).
 *
 * Bedanya dengan andalan: project ini belum punya model Outstanding / Transaksi,
 * jadi register hanya menulis detail_linen + config_linen. Jejak perubahannya tetap
 * tercatat otomatis lewat spatie/laravel-activitylog karena DetailLinen memakai
 * LogsActivity.
 *
 * Aturan kepemilikan (lihat juga App\Models\ConfigLinen):
 *   - FREE      → nol baris config_linen (bebas dipakai RS mana pun),
 *                 holder + ruangan default Global BAC / Ruang Global.
 *   - DEDICATED → satu RS pemilik, ruangan disimpan.
 *   - GROUP     → semua anggota group_rs pemilik, holder + ruangan default
 *                 Global BAC / Ruang Global.
 * Holder DEDICATED = RS pengirim pertama — dipakai transaksi sebagai
 * rs_ori untuk flag beda_rs.
 *
 * Pemakaian: RegisterLinenAction::run($request->validated());
 * Return: payload array {code, status, message, data} — data berisi
 * ['linen' => Collection DetailLinen, 'no_register' => string, 'keterangan' => ?string].
 */
class RegisterLinenAction
{
    use AsAction, PayloadTrait;

    public function handle(array $data): array
    {
        try {
            $result = $this->register($data);
        } catch (ValidationException $th) {
            // Biarkan handler global (bootstrap/app.php) yang memformat jadi 422.
            throw $th;
        } catch (QueryException $th) {
            report($th);

            return $this->payload(TOAST_FAILED, $this->readableDatabaseError($th));
        } catch (\Throwable $th) {
            report($th);

            return $this->payload(TOAST_FAILED, $th->getMessage());
        }

        return $this->payload(TOAST_SUCCESS, $result);
    }

    /**
     * @return array{linen: Collection<int, DetailLinen>, no_register: string, keterangan: ?string}
     */
    private function register(array $data): array
    {
        $rfid = $this->normalizeRfid($data['rfid'] ?? []);
        $owners = $this->normalizeIds($data['rs_id'] ?? []);

        // Type boleh dikirim eksplisit; kalau tidak, disimpulkan dari jumlah RS
        // yang dikirim (0 → FREE, 1 → DEDICATED, ≥2 → GROUP).
        $kepemilikan = $data['status_kepemilikan'] ?? $this->inferOwnership($owners);
        $owners = $this->resolveOwners($kepemilikan, $owners);

        // Holder GROUP/FREE = milik kolektif → default Global BAC, ruangan
        // default Ruang Global. DEDICATED ikut RS + ruangan kiriman.
        // (transaksi_rs_ori ikut holder ini untuk flag beda_rs.)
        $currentRs = $kepemilikan === RsStatusEnum::DEDICATED
            ? ($owners[0] ?? null)
            : $this->globalRsId();
        $ruangan = $kepemilikan === RsStatusEnum::DEDICATED
            ? ($data['ruangan_id'] ?? null)
            : $this->globalRuanganId();

        $statusRegister = $data['status_register'] ?? RegisterEnum::REGISTER;

        // GANTI_CHIP = chip lama diganti, linen dianggap masuk kembali sebagai KOTOR
        // (sama seperti andalan: GANTI_CHIP → transaksi KOTOR, selain itu REGISTER).
        $statusLinen = $statusRegister === RegisterEnum::GANTI_CHIP
            ? LinenStatusEnum::KOTOR
            : LinenStatusEnum::REGISTER;

        // Keterangan dari textarea desktop (opsional) — disalin ke tiap
        // baris detail_linen.
        $keterangan = trim((string) ($data['keterangan'] ?? $data['deskripsi'] ?? ''));
        $keterangan = $keterangan === '' ? null : $keterangan;

        DB::beginTransaction();

        try {
            $this->guardDuplikat($rfid);

            // Nomor register pakai waktu + suffix acak (bukan dari database):
            // REG-YYYYMMDDHHMMSS-XXXX. Cek tabrakan hanya sebagai pengaman.
            $now = now();
            $noRegister = 'REG-'.$now->format('YmdHis').'-'.str_pad((string) random_int(0, 9999), 4, '0', STR_PAD_LEFT);
            $try = 0;
            while ($try < 3 && DetailLinen::where(DetailLinen::field_no_register(), $noRegister)->exists()) {
                $noRegister = 'REG-'.$now->format('YmdHis').'-'.str_pad((string) random_int(0, 9999), 4, '0', STR_PAD_LEFT);
                $try++;
            }

            foreach ($rfid as $code) {
                DetailLinen::create([
                    DetailLinen::field_primary() => $code,
                    DetailLinen::field_rs_id() => $currentRs,
                    DetailLinen::field_ruangan_id() => $ruangan,
                    DetailLinen::field_jenis_id() => $data['jenis_id'] ?? null,
                    DetailLinen::field_bahan_id() => $data['bahan_id'] ?? null,
                    DetailLinen::field_supplier_id() => $data['supplier_id'] ?? null,
                    DetailLinen::field_description() => $keterangan,
                    DetailLinen::field_no_register() => $noRegister,
                    DetailLinen::field_status_cuci() => $data['status_cuci'] ?? null,
                    DetailLinen::field_status_register() => $statusRegister,
                    DetailLinen::field_status_kepemilikan() => $kepemilikan,
                    DetailLinen::field_status_linen() => $statusLinen,
                    DetailLinen::field_cek() => $data['tgl_cek'] ?? null,
                ]);

                // config_linen = MASTER kepemilikan, sumber kebenaran "RFID ini boleh
                // dipakai RS mana saja" (lihat ConfigLinen::isAllowed()):
                //   FREE      → nol baris  → bebas dipakai semua RS
                //   DEDICATED → satu baris → hanya RS itu
                //   GROUP     → beberapa baris → hanya anggota group tsb
                // detail_linen.detail_id_rs = CURRENT holder, diisi di atas.
                ConfigLinen::syncRs($code, $owners);
            }

            DB::commit();
            DashboardCache::flush();
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }

        return [
            'linen' => DetailLinen::with(['hasRs', 'hasRuangan', 'hasJenis', 'hasBahan', 'hasSupplier'])
                ->whereIn(DetailLinen::field_primary(), $rfid)
                ->get(),
            'no_register' => $noRegister,
            'keterangan' => $keterangan,
        ];
    }

    /**
     * Tebak kepemilikan bila klien tidak mengirim status_kepemilikan:
     *   - 0 RS   → FREE (bebas, nol baris config_linen).
     *   - 1 RS   → ikut tipe RS itu sendiri: FREE → FREE, GROUP (+anggota)
     *             → GROUP, selain itu DEDICATED.
     *   - ≥2 RS  → GROUP.
     */
    private function inferOwnership(array $owners): string
    {
        return match (count($owners)) {
            0 => RsStatusEnum::FREE,
            1 => $this->inferSingleOwner($owners[0]),
            default => RsStatusEnum::GROUP,
        };
    }

    private function inferSingleOwner(int $rsId): string
    {
        $rs = Rs::query()->select(['rs_status', 'rs_id_group'])->where('rs_id', $rsId)->first();

        // RS tidak ada → biarkan DEDICATED, resolveOwners yang 422-kan.
        if ($rs === null) {
            return RsStatusEnum::DEDICATED;
        }

        if (($rs->rs_status ?? null) === RsStatusEnum::FREE) {
            return RsStatusEnum::FREE;
        }

        if (($rs->rs_status ?? null) === RsStatusEnum::GROUP && $rs->rs_id_group) {
            return RsStatusEnum::GROUP;
        }

        return RsStatusEnum::DEDICATED;
    }

    private function resolveOwners(string $kepemilikan, array $owners): array
    {
        if ($kepemilikan === RsStatusEnum::FREE) {
            return [];
        }

        // GROUP dari 1 RS: kembangkan ke semua anggota group_rs yang sama
        // (kiriman tetap pertama = current holder).
        if ($kepemilikan === RsStatusEnum::GROUP && count($owners) === 1) {
            $owners = $this->expandGroupMembers($owners[0]);
        }

        $min = $kepemilikan === RsStatusEnum::GROUP ? 2 : 1;

        if (count($owners) < $min) {
            throw ValidationException::withMessages([
                'rs_id' => $kepemilikan === RsStatusEnum::GROUP
                    ? 'Kepemilikan GROUP wajib mengirim minimal 2 RS pemilik pada rs_id.'
                    : 'Kepemilikan DEDICATED wajib mengirim minimal 1 RS pemilik pada rs_id.',
            ]);
        }

        $missing = array_diff($owners, Rs::whereIn('rs_id', $owners)->pluck('rs_id')->all());

        if ($missing !== []) {
            throw ValidationException::withMessages([
                'rs_id' => 'RS tidak ditemukan: '.implode(', ', $missing).'.',
            ]);
        }

        return $owners;
    }

    /**
     * Anggota group_rs yang sama (termasuk RS kiriman, tetap di depan).
     * Grup beranggota tunggal → kembalikan apa adanya supaya validasi
     * min 2 RS menolak dengan pesan yang jelas, bukan diam-diam DEDICATED.
     */
    private function expandGroupMembers(int $rsId): array
    {
        $groupId = Rs::where('rs_id', $rsId)->value('rs_id_group');

        if (! $groupId) {
            return [$rsId];
        }

        $members = Rs::where('rs_id_group', $groupId)->orderBy('rs_id')->pluck('rs_id')->all();

        return array_values(array_unique(array_merge([$rsId], $members)));
    }

    /**
     * ID default holder (Global BAC, rs_code BAC) dan ruangan default
     * (Ruang Global, ruangan_code Global) untuk register GROUP/FREE.
     * Jatuh ke id 1 bila baris code belum ada (konvensi seed).
     */
    private function globalRsId(): ?int
    {
        return Rs::where('rs_code', 'BAC')->value('rs_id') ?? 1;
    }

    private function globalRuanganId(): ?int
    {
        return Ruangan::where('ruangan_code', 'Global')->value('ruangan_id') ?? 1;
    }

    /**
     * Tolak lebih awal supaya pesan errornya rapi, bukan pesan constraint MySQL.
     */
    private function guardDuplikat(array $rfid): void
    {
        $duplikat = DetailLinen::whereIn(DetailLinen::field_primary(), $rfid)
            ->pluck(DetailLinen::field_primary())
            ->all();

        if ($duplikat === []) {
            return;
        }

        $preview = implode(', ', array_slice($duplikat, 0, 10));
        $sisa = count($duplikat) - 10;

        throw ValidationException::withMessages([
            'rfid' => 'RFID sudah terdaftar: '.$preview.($sisa > 0 ? " (+{$sisa} lainnya)" : '').'.',
        ]);
    }

    /**
     * Trim, buang kosong, dan hilangkan duplikat tanpa mengubah urutan kiriman.
     */
    private function normalizeRfid(mixed $raw): array
    {
        $items = is_array($raw) ? $raw : [$raw];

        $rfid = [];

        foreach ($items as $item) {
            $code = trim((string) $item);

            if ($code !== '') {
                $rfid[$code] = $code;
            }
        }

        return array_values($rfid);
    }

    private function normalizeIds(mixed $raw): array
    {
        $items = is_array($raw) ? $raw : [$raw];

        return array_values(array_unique(array_filter(array_map(
            fn ($value) => is_numeric($value) ? (int) $value : null,
            $items
        ))));
    }

    /**
     * Ubah error constraint MySQL jadi pesan yang bisa dibaca klien
     * (pola yang sama dipakai andalan untuk error RFID duplikat).
     */
    private function readableDatabaseError(QueryException $th): string
    {
        if ((string) $th->getCode() === '23000') {
            $message = explode('for key', $th->getMessage());

            return str_replace(
                'SQLSTATE[23000]: Integrity constraint violation: 1062',
                'RFID',
                $message[0]
            );
        }

        return $th->getMessage();
    }
}
