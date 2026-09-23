<?php

namespace App\Dao\Enums;

use App\Dao\Traits\StatusTrait;
use BenSampo\Enum\Contracts\LocalizedEnum;
use BenSampo\Enum\Enum as Enum;

class CetakType extends Enum implements LocalizedEnum
{
    use StatusTrait;

    const Unknown = 0;

    const Barcode = 1;

    const Delivery = 2;
}
