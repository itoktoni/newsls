<?php

namespace App\Dao\Entities;

trait CetakEntity
{
    public static function field_primary()
    {
        return 'cetak_id';
    }

    public function getFieldPrimaryAttribute()
    {
        return $this->{$this->field_primary()};
    }

    public static function field_name()
    {
        return 'cetak_code';
    }

    public function getFieldNameAttribute()
    {
        return $this->{$this->field_name()};
    }

    public static function field_user()
    {
        return 'cetak_user';
    }

    public function getFieldUserAttribute()
    {
        return $this->{$this->field_user()};
    }

    public static function field_date()
    {
        return 'cetak_date';
    }

    public function getFieldDateAttribute()
    {
        return $this->{$this->field_date()} ? $this->{$this->field_date()}->format('Y-m-d') : null;
    }

    public static function field_rs_id()
    {
        return 'cetak_id_rs';
    }

    public function getFieldRsIdAttribute()
    {
        return $this->{$this->field_rs_id()};
    }

    public static function field_ruangan_id()
    {
        return 'cetak_id_ruangan';
    }

    public function getFieldRuanganIdAttribute()
    {
        return $this->{$this->field_ruangan_id()};
    }

    public static function field_type()
    {
        return 'cetak_type';
    }

    public function getFieldTypeAttribute()
    {
        return $this->{$this->field_type()};
    }

    public static function field_barcode()
    {
        return 'cetak_barcode';
    }

    public function getFieldBarcodeAttribute()
    {
        return $this->{$this->field_barcode()};
    }

    public static function field_delivery()
    {
        return 'cetak_delivery';
    }

    public function getFieldDeliveryAttribute()
    {
        return $this->{$this->field_delivery()};
    }
}
