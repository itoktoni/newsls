<?php

namespace App\Dao\Enums;

use App\Dao\Traits\StatusTrait;
use BenSampo\Enum\Contracts\LocalizedEnum;
use BenSampo\Enum\Enum as Enum;

class FilterType extends Enum implements LocalizedEnum
{
    use StatusTrait;

    const Kotor = 'KOTOR';

    const Reject = 'REJECT';

    const Rewash = 'REWASH';

    const ScanRs = 'SCAN';

    const Pending = 'PENDING';

    const Hilang = 'HILANG';

}
