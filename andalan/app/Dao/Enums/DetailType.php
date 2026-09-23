<?php

namespace App\Dao\Enums;

use App\Dao\Traits\StatusTrait;
use BenSampo\Enum\Contracts\LocalizedEnum;
use BenSampo\Enum\Enum as Enum;

class DetailType extends Enum implements LocalizedEnum
{
    use StatusTrait;

    const Kotor = 1;

    const Retur = 2;

    const Rewash = 3;

    const BersihKotor = 4;

    const BersihRetur = 5;

    const BersihRewash = 6;

    const Register = 7;

    const LinenBaru = 100;

    const Pending = 70;

    const Hilang = 80;
}
