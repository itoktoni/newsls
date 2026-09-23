<?php

namespace App\Enums;

use App\Concerns\EnumTrait;
use BenSampo\Enum\Enum;

final class CuciEnum extends Enum
{
    use EnumTrait;

    const CUCI = 'CUCI';

    const RENTAL = 'RENTAL';

    public static function getDescription(mixed $value): string
    {
        return match ($value) {
            self::CUCI => 'Cuci',
            self::RENTAL => 'Rental',
            default => parent::getDescription($value),
        };
    }
}
