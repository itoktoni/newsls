<?php

namespace App\Dao\Entities;

trait ViewInvoiceEntity
{
    public static function field_primary()
    {
        return 'view_key';
    }

    public function getFieldPrimaryAttribute()
    {
        return $this->{$this->field_primary()};
    }

    public static function field_name()
    {
        return 'view_linen_nama';
    }

    public function getFieldNameAttribute()
    {
        return $this->{$this->field_name()};
    }

    public static function field_rs_id()
    {
        return 'view_rs_id';
    }

    public function getFieldRsIdAttribute()
    {
        return $this->{$this->field_rs()};
    }

    public static function field_rs()
    {
        return 'view_rs_nama';
    }

    public function getFieldRsAttribute()
    {
        return $this->{$this->field_rs()};
    }

    public static function field_tanggal()
    {
        return 'view_tanggal';
    }

    public function getFieldTanggalAttribute()
    {
        return $this->{$this->field_tanggal()};
    }

    public static function field_qty()
    {
        return 'view_qty';
    }

    public function getFieldQtyAttribute()
    {
        return $this->{$this->field_qty()};
    }

    public static function field_berat()
    {
        return 'view_berat';
    }

    public function getFieldBeratAttribute()
    {
        return $this->{$this->field_berat()};
    }

    public static function field_harga_cuci()
    {
        return 'view_harga_cuci';
    }

    public function getFieldHargaCuciAttribute()
    {
        return $this->{$this->field_harga_cuci()};
    }

    public static function field_harga_sewa()
    {
        return 'view_harga_sewa';
    }

    public function getFieldHargaSewaAttribute()
    {
        return $this->{$this->field_harga_sewa()};
    }
}
