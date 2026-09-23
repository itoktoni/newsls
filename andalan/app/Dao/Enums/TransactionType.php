<?php

namespace App\Dao\Enums;

use App\Dao\Traits\StatusTrait;
use BenSampo\Enum\Contracts\LocalizedEnum;
use BenSampo\Enum\Enum as Enum;

class TransactionType extends Enum implements LocalizedEnum
{
    use StatusTrait;

    const UNKNOWN = null;

    const KOTOR = 'KOTOR';

    const REJECT = 'REJECT';

    const REWASH = 'REWASH';

    const BERSIH = 'BERSIH';

    const REGISTER = 'REGISTER';

    public static function getDescription($value): string
    {
        if ($value === self::UNKNOWN) {
            return '';
        }

        return parent::getDescription($value);
    }
}
