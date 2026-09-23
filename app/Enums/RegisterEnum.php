<?php

namespace App\Enums;

use App\Concerns\EnumTrait;
use BenSampo\Enum\Enum;

final class RegisterEnum extends Enum
{
    use EnumTrait;

    const REGISTER = 'REGISTER';

    const GANTI_CHIP = 'GANTI_CHIP';

    public static function getDescription(mixed $value): string
    {
        return match ($value) {
            self::REGISTER => 'Register',
            self::GANTI_CHIP => 'Ganti Chip',
            default => parent::getDescription($value),
        };
    }
}
