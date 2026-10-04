<?php

namespace App\Enums;

use App\Concerns\EnumTrait;
use BenSampo\Enum\Enum;

final class TransactionType extends Enum
{
    use EnumTrait;

    const KOTOR = 'KOTOR';

    // const REJECT = 'REJECT';
    // const REWASH = 'REWASH';
    // const RETUR = self::REJECT;

    const BERSIH = 'BERSIH';

    const REGISTER = 'REGISTER';


    public static function getDescription(mixed $value): string
    {
        return match ($value) {
            // self::REJECT, self::RETUR => 'Retur',
            // self::REWASH => 'Rewash',
            self::KOTOR => 'Kotor',
            self::BERSIH => 'Bersih',
            self::REGISTER => 'Register',
            default => parent::getDescription($value),
        };
    }
}
