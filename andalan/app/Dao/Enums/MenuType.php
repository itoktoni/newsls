<?php

namespace App\Dao\Enums;

use App\Dao\Traits\StatusTrait;
use BenSampo\Enum\Contracts\LocalizedEnum;
use BenSampo\Enum\Enum as Enum;

class MenuType extends Enum implements LocalizedEnum
{
    use StatusTrait;

    const Menu = 1;

    const Group = 2;

    const Internal = 3;

    const External = 4;

    const Devider = 5;
}
