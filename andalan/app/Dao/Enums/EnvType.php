<?php

namespace App\Dao\Enums;

use App\Dao\Traits\StatusTrait;
use BenSampo\Enum\Contracts\LocalizedEnum;
use BenSampo\Enum\Enum as Enum;

class EnvType extends Enum implements LocalizedEnum
{
    use StatusTrait;

    const Local = 'local';

    const Production = 'production';

    const Staging = 'staging';

    const Testing = 'testing';

    const Mirror = 'mirror';
}
