<?php

namespace App\Properties;

trait JenisBahanEntity
{
    public static function field_description()
    {
        return 'bahan_deskripsi';
    }

    public function getFieldDescriptionAttribute()
    {
        return $this->{static::field_description()};
    }
}
