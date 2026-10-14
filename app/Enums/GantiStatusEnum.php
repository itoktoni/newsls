<?php

namespace App\Enums;

use App\Concerns\EnumTrait;
use BenSampo\Enum\Enum;

/**
 * Status penggantian chip (tab desktop "Ganti Pending" → combo_box_pending_status
 * → kolom transaksi.transaksi_ganti_status).
 */
final class GantiStatusEnum extends Enum
{
    use EnumTrait;

    const NODA = 'NODA';

    const RUSAK = 'RUSAK';

    const HILANG = 'HILANG';

    const PENDING = 'PENDING';

    public static function getDescription(mixed $value): string
    {
        return match ($value) {
            self::NODA => 'Noda',
            self::RUSAK => 'Rusak',
            self::HILANG => 'Hilang',
            self::PENDING => 'Pending',
            default => parent::getDescription($value),
        };
    }
}
