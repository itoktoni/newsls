<?php

namespace App\Properties;

trait SupplierEntity
{
    public static function field_alamat()
    {
        return 'supplier_alamat';
    }

    public function getFieldAlamatAttribute()
    {
        return $this->{static::field_alamat()};
    }

    public static function field_phone()
    {
        return 'supplier_phone';
    }

    public function getFieldPhoneAttribute()
    {
        return $this->{static::field_phone()};
    }

    public static function field_contact()
    {
        return 'supplier_kontak';
    }

    public function getFieldContactAttribute()
    {
        return $this->{static::field_contact()};
    }

    public static function field_email()
    {
        return 'supplier_email';
    }

    public function getFieldEmailAttribute()
    {
        return $this->{static::field_email()};
    }
}
