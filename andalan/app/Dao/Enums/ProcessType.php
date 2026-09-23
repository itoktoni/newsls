<?php

namespace App\Dao\Enums;

use App\Dao\Traits\StatusTrait;
use BenSampo\Enum\Contracts\LocalizedEnum;
use BenSampo\Enum\Enum as Enum;

class ProcessType extends Enum implements LocalizedEnum
{
    use StatusTrait;

    const UNKNOWN = null;

    const REGISTER = 'REGISTER';

    const KOTOR = 'KOTOR';

    const SCAN = 'SCAN';

    const QC = 'QC';

    const PACKING = 'PACKING';

    const BERSIH = 'BERSIH';

    public static function getDescription($value): string
    {
        if ($value === self::QC) {
            return 'Quality Control';
        }

        return parent::getDescription($value);
    }
}
