<?php

namespace App\Enums;

use App\Concerns\EnumTrait;
use BenSampo\Enum\Enum;

final class TransactionType extends Enum
{
    use EnumTrait;

    const KOTOR = 'KOTOR';

    const REJECT = 'REJECT';

    const REWASH = 'REWASH';

    const BERSIH = 'BERSIH';

    // Alias UI: RETUR = REJECT (legacy nama)
    const RETUR = self::REJECT;

    public static function getDescription(mixed $value): string
    {
        return match ($value) {
            self::KOTOR => 'Kotor',
            self::REJECT, self::RETUR => 'Retur',
            self::REWASH => 'Rewash',
            self::BERSIH => 'Bersih',
            default => parent::getDescription($value),
        };
    }
}
