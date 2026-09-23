<?php

namespace App\Properties;

trait RuanganEntity
{
    public static function field_description()
    {
        return 'ruangan_deskripsi';
    }

    public function getFieldDescriptionAttribute()
    {
        return $this->{static::field_description()};
    }

    public static function field_code()
    {
        return 'ruangan_code';
    }

    public function getFieldCodeAttribute()
    {
        return $this->{static::field_code()};
    }
}
