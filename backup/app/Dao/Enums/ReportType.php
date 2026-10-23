<?php

namespace App\Dao\Enums;

use App\Dao\Traits\StatusTrait;
use BenSampo\Enum\Contracts\LocalizedEnum;
use BenSampo\Enum\Enum as Enum;

class ReportType extends Enum implements LocalizedEnum
{
    use StatusTrait;

    const Pdf = 1;

    const Html = 2;
}
