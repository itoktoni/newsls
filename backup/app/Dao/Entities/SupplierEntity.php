<?php

namespace App\Dao\Entities;

trait SupplierEntity
{
    public static function field_primary()
    {
        return 'supplier_id';
    }

    public function getFieldPrimaryAttribute()
    {
        return $this->{$this->field_primary()};
    }

    public static function field_name()
    {
        return 'supplier_nama';
    }

    public function getFieldNameAttribute()
    {
        return $this->{$this->field_name()};
    }

    public static function field_alamat()
    {
        return 'supplier_alamat';
    }

    public function getFieldAlamatAttribute()
    {
        return $this->{$this->field_alamat()};
    }

    public static function field_phone()
    {
        return 'supplier_phone';
    }

    public function getFieldPhoneAttribute()
    {
        return $this->{$this->field_phone()};
    }

    public static function field_contact()
    {
        return 'supplier_kontak';
    }

    public function getFieldContactAttribute()
    {
        return $this->{$this->field_contact()};
    }

    public static function field_email()
    {
        return 'supplier_email';
    }

    public function getFieldEmailAttribute()
    {
        return $this->{$this->field_email()};
    }
}
