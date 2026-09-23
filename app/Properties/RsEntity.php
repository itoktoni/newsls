<?php

namespace App\Properties;

trait RsEntity
{
    public static function field_group()
    {
        return 'rs_id_group';
    }

    public function getFieldGroupAttribute()
    {
        return $this->{static::field_group()};
    }

    public static function field_description()
    {
        return 'rs_deskripsi';
    }

    public function getFieldDescriptionAttribute()
    {
        return $this->{static::field_description()};
    }

    public static function field_alamat()
    {
        return 'rs_alamat';
    }

    public function getFieldAlamatAttribute()
    {
        return $this->{static::field_alamat()};
    }

    public static function field_harga_cuci()
    {
        return 'rs_harga_cuci';
    }

    public function getFieldHargaCuciAttribute()
    {
        return $this->{static::field_harga_cuci()};
    }

    public static function field_harga_sewa()
    {
        return 'rs_harga_sewa';
    }

    public function getFieldHargaSewaAttribute()
    {
        return $this->{static::field_harga_sewa()};
    }

    public static function field_code()
    {
        return 'rs_code';
    }

    public function getFieldCodeAttribute()
    {
        return $this->{static::field_code()};
    }

    public static function field_status()
    {
        return 'rs_status';
    }

    public function getFieldStatusAttribute()
    {
        return $this->{static::field_status()};
    }
}
