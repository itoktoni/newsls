<?php

namespace App\Models;

use App\Properties\ConfigLinenEntity;

class ConfigLinen extends BaseModel
{
    use ConfigLinenEntity;

    protected $table = 'config_linen';

    protected $primaryKey = 'detail_rfid';

    public $timestamps = false;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'rs_id',
        'detail_rfid',
    ];

    public static $filterColumns = ['detail_rfid', 'rs_id'];

    public static $sortColumns = ['detail_rfid', 'rs_nama', 'kepemilikan'];

    protected function casts(): array
    {
        return [
            'rs_id' => 'integer',
            'detail_rfid' => 'string',
        ];
    }

    public static function field_name(): string
    {
        return 'detail_rfid';
    }

    public function rules(): array
    {
        return [
            'detail_rfid' => 'required|string|max:255',
            'rs_id' => 'nullable|integer|exists:rs,rs_id',
        ];
    }

    public function hasRs()
    {
        return $this->hasOne(Rs::class, 'rs_id', 'rs_id');
    }

    public function hasDetail()
    {
        return $this->hasOne(DetailLinen::class, 'detail_rfid', 'detail_rfid');
    }

    // ponytail: dua peran yang sengaja dipisah —
    // - config_linen  = MASTER: RFID ini milik siapa (baris = RS pemilik sah).
    // - detail_linen.detail_id_rs = CURRENT: RFID ini sedang dipegang RS mana.
    // Keduanya boleh berbeda (FREE dipinjam RS lain / GROUP tukar antar anggota).
    public static function rsIdsFor(string $rfid): array
    {
        return static::where('detail_rfid', $rfid)->pluck('rs_id')->all();
    }

    // RS yang sekarang memegang RFID (current holder) — boleh null untuk FREE.
    public static function currentRsId(string $rfid): ?int
    {
        $rsId = DetailLinen::where('detail_rfid', $rfid)->value('detail_id_rs');

        return $rsId === null ? null : (int) $rsId;
    }

    // Pemilik sah (master) dalam bentuk label RS, dipakai view tabel.
    public static function ownersLabel(string $rfid): string
    {
        $names = Rs::whereIn('rs_id', static::rsIdsFor($rfid))->pluck('rs_nama')->all();

        return $names === [] ? 'FREE (bebas)' : implode(', ', $names);
    }

    // ponytail: config_linen = MASTER kepemilikan (registry siapa saja pemilik sah
    // RFID ini). detail_linen.detail_id_rs = CURRENT — RS yang sedang memakai
    // linen saat ini, boleh beda dari pemilik (mis. FREE dipinjam RS lain,
    // GROUP bertukar antar anggota). Jadi sync di sini TIDAK menyentuh
    // detail_id_rs — itu diisi manual di form Data Linen / via scan.
    public static function syncRs(string $rfid, array $rsIds): void
    {
        $rsIds = array_values(array_unique(array_map('intval', $rsIds)));

        static::where('detail_rfid', $rfid)->delete();

        foreach ($rsIds as $rsId) {
            static::create(['detail_rfid' => $rfid, 'rs_id' => $rsId]);
        }
    }

    public static function clearRfid(string $rfid): void
    {
        static::where('detail_rfid', $rfid)->delete();
    }

    // Aturan kepemilikan (RsStatusEnum):
    // - DEDICATED: hanya RS yang terdaftar di config_linen untuk RFID tsb
    //   (beda-RS = salah, mis. scan di RS lain berarti ada yang salah).
    // - GROUP: sama — daftar RS di config = anggota group, boleh tukar
    //   (mis. siloam A + siloam B pakai linen yang sama).
    // - FREE: nol baris config — bebas dipakai RS lain kecuali kepemilikan
    //   DEDICATED/GROUP (ditandai lewat baris config).
    // ponytail: data lama ada config orphan (RFID di config tapi tidak di
    // detail_linen, mis. E280689400004025AAE544BA) — diperlakukan sebagai
    // GROUP whitelist agar tidak terkunci total sebelum re-register.
    public static function isAllowed(string $rfid, int $rsId): bool
    {
        $detail = DetailLinen::where('detail_rfid', $rfid)->first();

        if ($detail === null) {
            return static::where('detail_rfid', $rfid)->where('rs_id', $rsId)->exists();
        }

        if (($detail->detail_status_kepemilikan ?? 'FREE') === 'FREE') {
            return true;
        }

        return static::where('detail_rfid', $rfid)->where('rs_id', $rsId)->exists();
    }
}
