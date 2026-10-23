<?php

namespace App\Dao\Enums;

use App\Dao\Traits\StatusTrait;
use BenSampo\Enum\Contracts\LocalizedEnum;
use BenSampo\Enum\Enum as Enum;

class UserType extends Enum implements LocalizedEnum
{
    use StatusTrait;

    const FromUser = 1;

    const CustomField = 2;
}
