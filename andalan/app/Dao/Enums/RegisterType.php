<?php

namespace App\Dao\Enums;

use App\Dao\Traits\StatusTrait;
use BenSampo\Enum\Contracts\LocalizedEnum;
use BenSampo\Enum\Enum as Enum;

class RegisterType extends Enum implements LocalizedEnum
{
    use StatusTrait;

    const UNKNOWN = null;

    const REGISTER = 'REGISTER';

    const GANTI_CHIP = 'GANTI_CHIP';

    public static function getDescription($value): string
    {
        if ($value === self::REGISTER) {
            return 'Register';
        }

        if ($value === self::GANTI_CHIP) {
            return 'Ganti Chip';
        }

        return parent::getDescription($value);
    }
}
