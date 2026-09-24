<?php

namespace App\Enums;

use App\Concerns\EnumTrait;
use BenSampo\Enum\Enum;

/**
 * Tipe log aktivitas — disimpan di kolom activity_log.log_name.
 *
 * Nilai diadopsi dari enum LogType desktop (App.Dao.Enums) supaya web dan
 * desktop bicara bahasa yang sama. Desktop mengirim operasi via API, web
 * menulis log_name = salah satu nilai di sini agar activity-log/table
 * sekali lihat mencerminkan event-nya (REGISTER, KOTOR, PACKING, ...).
 *
 * Konstanta bertanda "tambahan web" tidak ada di enum desktop — dibutuhkan
 * untuk operasi yang hanya terjadi di sisi web/API.
 */
final class LogType extends Enum
{
    use EnumTrait;

    const UNKNOWN = null;

    const REGISTER = 'REGISTER';

    const GANTI_LINEN = 'GANTI_LINEN';

    const KOTOR = 'KOTOR';

    const SCAN = 'SCAN';

    const QC = 'QC';

    const QC_TRANSACTION = 'QC_WITH_INSERT_TO_TRANSACTION';

    const REG_TRANSACTION = 'REG_WITH_INSERT_TO_TRANSACTION';

    const PACKING = 'PACKING';

    const PENDING = 'PENDING';

    const HILANG = 'HILANG';

    const DELETE_DETAIL = 'DELETE_DETAIL';

    const DELETE_TRANSAKSI = 'DELETE_TRANSAKSI';

    const DELETE_BARCODE = 'DELETE_BARCODE';

    // ponytail: alias desktop (DELETE_RFID = DELETE_BARCODE di sana).
    // Web TIDAK memakainya untuk hapus linen — hapus RFID/detail linen
    // memakai DELETE_DETAIL di atas. Alias ini dipertahankan apa adanya
    // agar kontrak enum desktop tidak bergeser.
    const DELETE_RFID = 'DELETE_BARCODE';

    const BERSIH = 'BERSIH';

    const OPNAME = 'OPNAME';

    // Tambahan web: update data linen via form (DetailLinen updated).
    const UPDATE = 'UPDATE';

    // Tambahan web: hasil map TransactionType::REJECT/REWASH di API transaksi.
    const RETUR = 'RETUR';

    const REWASH = 'REWASH';

    // Tambahan web: pengiriman bersih ke RS (API delivery).
    const DELIVERY = 'DELIVERY';

    // Tambahan web: grouping QC (GET /api/grouping/{rfid}). Desktop memakai
    // QC / ObsesimanType::Grouping; web menulis log_name eksplisit per operasi.
    const GROUPING = 'GROUPING';

    public static function getDescription(mixed $value): string
    {
        return match ($value) {
            null, self::UNKNOWN => 'Unknown',
            self::REGISTER => 'Register',
            self::GANTI_LINEN => 'Ganti Linen',
            self::KOTOR => 'Kotor',
            self::SCAN => 'Scan',
            self::QC => 'QC',
            self::QC_TRANSACTION => 'QC dengan Transaksi',
            self::REG_TRANSACTION => 'Register dengan Transaksi',
            self::PACKING => 'Packing',
            self::PENDING => 'Pending',
            self::HILANG => 'Hilang',
            self::DELETE_DETAIL => 'Hapus Detail',
            self::DELETE_TRANSAKSI => 'Hapus Transaksi',
            self::DELETE_BARCODE, self::DELETE_RFID => 'Hapus Barcode',
            self::BERSIH => 'Bersih',
            self::OPNAME => 'Opname',
            self::UPDATE => 'Update',
            self::RETUR => 'Retur',
            self::REWASH => 'Rewash',
            self::DELIVERY => 'Delivery',
            self::GROUPING => 'Grouping',
            default => parent::getDescription($value),
        };
    }
}
