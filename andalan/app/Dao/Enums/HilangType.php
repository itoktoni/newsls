<?php

namespace App\Dao\Enums;

use App\Dao\Traits\StatusTrait;
use BenSampo\Enum\Contracts\LocalizedEnum;
use BenSampo\Enum\Enum as Enum;

class HilangType extends Enum implements LocalizedEnum
{
    use StatusTrait;

    const UNKNOWN = null;

    const NORMAL = 'NORMAL';

    const PENDING = 'PENDING';

    const HILANG = 'HILANG';

    public static function getDescription($value): string
    {
        if ($value === self::UNKNOWN) {
            return '';
        }

        return parent::getDescription($value);
    }
}
