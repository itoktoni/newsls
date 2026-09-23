<?php

namespace App\Actions;

use App\Concerns\PayloadTrait;
use App\Enums\LinenStatusEnum;
use App\Enums\RegisterEnum;
use App\Enums\RsStatusEnum;
use App\Models\ConfigLinen;
use App\Models\DetailLinen;
use App\Models\Rs;
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
 * Aturan kepemilikan (lihat juga App\Models\ConfigLinen) — ditentukan jumlah rs_id:
 *   - FREE      → nol baris config_linen, linen bebas dipakai RS mana pun.
 *   - DEDICATED → satu RS pemilik.
 *   - GROUP     → beberapa RS pemilik (mis. siloam A + siloam B).
 *
 * Pemakaian: RegisterLinenAction::run($request->validated());
 * Return: payload array {code, status, message, data} — data berisi Collection
 * DetailLinen saat sukses, atau pesan error saat gagal.
 */
class RegisterLinenAction
{
    use AsAction, PayloadTrait;

    public function handle(array $data): array
    {
        try {
            $linen = $this->register($data);
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

        return $this->payload(TOAST_SUCCESS, $linen);
    }

    /**
     * @return Collection<int, DetailLinen> baris detail_linen yang baru dibuat
     */
    private function register(array $data): Collection
    {
        $rfid = $this->normalizeRfid($data['rfid'] ?? []);
        $owners = $this->normalizeIds($data['rs_id'] ?? []);

        // Type boleh dikirim eksplisit; kalau tidak, disimpulkan dari jumlah RS
        // yang dikirim (0 → FREE, 1 → DEDICATED, ≥2 → GROUP).
        $kepemilikan = $data['status_kepemilikan'] ?? $this->inferOwnership($owners);
        $owners = $this->resolveOwners($kepemilikan, $owners);

        // FREE tidak terikat RS mana pun (boleh dipakai semua RS) → detail_id_rs null.
        // DEDICATED/GROUP → pemegang saat register = pemilik pertama.
        $currentRs = $kepemilikan === RsStatusEnum::FREE ? null : $owners[0];

        $statusRegister = $data['status_register'] ?? RegisterEnum::REGISTER;

        // GANTI_CHIP = chip lama diganti, linen dianggap masuk kembali sebagai KOTOR
        // (sama seperti andalan: GANTI_CHIP → transaksi KOTOR, selain itu REGISTER).
        $statusLinen = $statusRegister === RegisterEnum::GANTI_CHIP
            ? LinenStatusEnum::KOTOR
            : LinenStatusEnum::REGISTER;

        DB::beginTransaction();

        try {
            $this->guardDuplikat($rfid);

            foreach ($rfid as $code) {
                DetailLinen::create([
                    DetailLinen::field_primary() => $code,
                    DetailLinen::field_rs_id() => $currentRs,
                    DetailLinen::field_ruangan_id() => $data['ruangan_id'] ?? null,
                    DetailLinen::field_jenis_id() => $data['jenis_id'] ?? null,
                    DetailLinen::field_bahan_id() => $data['bahan_id'] ?? null,
                    DetailLinen::field_supplier_id() => $data['supplier_id'] ?? null,
                    DetailLinen::field_description() => $data['deskripsi'] ?? null,
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

        return DetailLinen::with(['hasRs', 'hasRuangan', 'hasJenis', 'hasBahan', 'hasSupplier'])
            ->whereIn(DetailLinen::field_primary(), $rfid)
            ->get();
    }

    /**
     * Tebak kepemilikan dari jumlah RS pemilik yang dikirim klien.
     * Klien boleh menimpanya lewat field status_kepemilikan.
     */
    private function inferOwnership(array $owners): string
    {
        return match (count($owners)) {
            0 => RsStatusEnum::FREE,
            1 => RsStatusEnum::DEDICATED,
            default => RsStatusEnum::GROUP,
        };
    }

    private function resolveOwners(string $kepemilikan, array $owners): array
    {
        if ($kepemilikan === RsStatusEnum::FREE) {
            return [];
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
