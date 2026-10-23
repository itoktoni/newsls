<?php

namespace App\Dao\Enums;

use App\Dao\Traits\StatusTrait;
use BenSampo\Enum\Contracts\LocalizedEnum;
use BenSampo\Enum\Enum as Enum;

class BooleanType extends Enum implements LocalizedEnum
{
    use StatusTrait;

    const YES = 1;

    const NO = 0;
}
