<?php

namespace App\Properties;

trait TransaksiEntity
{
    public static function field_key()
    {
        return 'transaksi_key';
    }

    public function getFieldKeyAttribute()
    {
        return $this->{static::field_key()};
    }

    public static function field_rfid()
    {
        return 'transaksi_rfid';
    }

    public function getFieldRfidAttribute()
    {
        return $this->{static::field_rfid()};
    }

    public static function field_status()
    {
        return 'transaksi_status';
    }

    public function getFieldStatusAttribute()
    {
        return $this->{static::field_status()};
    }
}
