<?php

namespace App\Properties;

trait GroupRsEntity
{
    public static function field_nama()
    {
        return 'group_rs_nama';
    }

    public function getFieldNamaAttribute()
    {
        return $this->{static::field_nama()};
    }

    public static function field_code()
    {
        return 'group_rs_code';
    }

    public function getFieldCodeAttribute()
    {
        return $this->{static::field_code()};
    }

    public static function field_deskripsi()
    {
        return 'group_rs_deskripsi';
    }

    public function getFieldDeskripsiAttribute()
    {
        return $this->{static::field_deskripsi()};
    }
}
