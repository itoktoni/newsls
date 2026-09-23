<?php

namespace App\Enums;

use App\Concerns\EnumTrait;
use BenSampo\Enum\Enum;

final class LinenStatusEnum extends Enum
{
    use EnumTrait;

    const REGISTER = 'REGISTER';

    const KOTOR = 'KOTOR';

    const BERSIH = 'BERSIH';

    const GUDANG = 'GUDANG';

    public static function getDescription(mixed $value): string
    {
        return match ($value) {
            self::REGISTER => 'Register',
            self::KOTOR => 'Kotor',
            self::BERSIH => 'Bersih',
            self::GUDANG => 'Gudang',
            default => parent::getDescription($value),
        };
    }
}
