<?php

namespace App\Dao\Enums;

use App\Dao\Traits\StatusTrait;
use BenSampo\Enum\Contracts\LocalizedEnum;
use BenSampo\Enum\Enum as Enum;

class LogType extends Enum implements LocalizedEnum
{
    use StatusTrait;

    const UNKNOWN = null;

    const REGISTER = 'REGISTER';

    const GANTI_LINEN = 'GANTI_LINEN';

    const KOTOR = 'KOTOR';

    const SCAN = 'SCAN';

    const QC = 'QC';

    const QC_TRANSACTION = 'QC_WITH_INSERT_TO_TRANSACTION';

    const REG_TRANSACTION = 'REG_WITH_INSERT_TO_TRANSACTION';

    const PACKING = 'PACKING';

    const PENDING = 'PENDING';

    const HILANG = 'HILANG';

    const DELETE_TRANSAKSI = 'DELETE_TRANSAKSI';

    const DELETE_BARCODE = 'DELETE_BARCODE';

    const DELETE_RFID = 'DELETE_BARCODE';

    const BERSIH = 'BERSIH';

    const OPNAME = 'OPNAME';
}
