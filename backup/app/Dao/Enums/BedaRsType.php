<?php

namespace App\Dao\Enums;

use App\Dao\Traits\StatusTrait;
use BenSampo\Enum\Contracts\LocalizedEnum;
use BenSampo\Enum\Enum as Enum;

class BedaRsType extends Enum implements LocalizedEnum
{
    use StatusTrait;

    const YES = 'YA';

    const NO = 'TIDAK';

    const NOT_REGISTERED = 'NOT_REGISTER';
}
