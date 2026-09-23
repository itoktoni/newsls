<?php

namespace App\Properties;

trait ConfigLinenEntity
{
    public static function field_rs_id()
    {
        return 'rs_id';
    }

    public function getFieldRsIdAttribute()
    {
        return $this->{static::field_rs_id()};
    }

    public static function field_rfid()
    {
        return 'detail_rfid';
    }

    public function getFieldRfidAttribute()
    {
        return $this->{static::field_rfid()};
    }
}
