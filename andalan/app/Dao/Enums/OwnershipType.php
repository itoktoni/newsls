<?php

namespace App\Dao\Enums;

use App\Dao\Traits\StatusTrait;
use BenSampo\Enum\Contracts\LocalizedEnum;
use BenSampo\Enum\Enum as Enum;

class OwnershipType extends Enum implements LocalizedEnum
{
    use StatusTrait;

    const UNKNOWN = null;

    const FREE = 'FREE';

    const DEDICATED = 'DEDICATED';
}
