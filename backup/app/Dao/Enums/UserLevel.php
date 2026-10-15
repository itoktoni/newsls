<?php

namespace App\Dao\Enums;

use App\Dao\Traits\StatusTrait;
use BenSampo\Enum\Contracts\LocalizedEnum;
use BenSampo\Enum\Enum as Enum;

class UserLevel extends Enum implements LocalizedEnum
{
    use StatusTrait;

    const Pengguna = 1;

    const Operator = 10;

    const Operation = 11;

    const Finance = 20;

    const Admin = 30;

    const Owner = 40;

    const Developer = 100;
}
