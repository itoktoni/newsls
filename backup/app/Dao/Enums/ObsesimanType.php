<?php

namespace App\Dao\Enums;

use BenSampo\Enum\Enum as Enum;

class ObsesimanType extends Enum
{
    const StatusLinen = null;

    // const Register
    const Register = 1;

    const GantiChip = 2;

    const Rental = 3;

    const Cuci = 4;

    const GantiProduct = 5;

    const GantiRuangan = 6;

    const GantiRs = 7;

    // const Kotor
    const LinenKotor = 8;

    const BedaRs = 9;

    const BelumDiScan = 10;

    // const Retur
    const ChipRusak = 11;

    const LinenRusak = 12;

    const KelebihanStock = 13;

    // const Rewash
    const Bernoda = 14;

    const BahanUsang = 15;

    // const Pending Hilang
    const Grouping = 16;

    const Bersih = 17;

    const Pending = 18;

    const Hilang = 19;

    const Retur = 20;

    const Rewash = 21;

    // saat diupload
    const Download = 22;

    const Update = 23;

    const Upload = 24;

    const KirimRetur = 25;

    const KirimRewash = 26;

    // hapus chip
    const HapusChip = 27;

    const Activated = 28;

    public static function name()
    {
        return 'Linen Status';
    }

    public static function getDescription($value): string
    {
        if ($value === self::Grouping) {
            return 'Delivery';
        }

        return parent::getDescription($value);
    }
}
