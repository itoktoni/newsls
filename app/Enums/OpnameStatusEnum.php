<?php

namespace App\Enums;

use App\Concerns\EnumTrait;
use BenSampo\Enum\Enum;

/**
 * @method static static Proses()
 * @method static static Selesai()
 */
final class OpnameStatusEnum extends Enum
{
    use EnumTrait;

    const Proses = 1;

    const Selesai = 2;

    public static function getDescription(mixed $value): string
    {
        return match ((int) $value) {
            self::Proses => 'Proses',
            self::Selesai => 'Selesai',
            default => $value ? parent::getDescription($value) : 'Draft',
        };
    }
}
