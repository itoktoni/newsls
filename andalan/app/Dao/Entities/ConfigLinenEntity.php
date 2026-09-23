<?php

namespace App\Dao\Entities;

use App\Dao\Enums\CuciType;
use App\Dao\Enums\ProcessType;
use App\Dao\Enums\RegisterType;
use App\Dao\Enums\TransactionType;
use App\Dao\Models\JenisLinen;
use App\Dao\Models\Rs;
use App\Dao\Models\Ruangan;

trait ConfigLinenEntity
{
    public static function field_primary()
    {
        return 'detail_rfid';
    }

    public function getFieldPrimaryAttribute()
    {
        return $this->{$this->field_primary()};
    }

    public static function field_rs_id()
    {
        return 'rs_id';
    }

    public static function field_name()
    {
        return 'detail_rfid';
    }

    public function getFieldNameAttribute()
    {
        return $this->{$this->field_name()};
    }
}
