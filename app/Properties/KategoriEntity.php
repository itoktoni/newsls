<?php

namespace App\Properties;

trait KategoriEntity
{
    public static function field_description()
    {
        return 'kategori_deskripsi';
    }

    public function getFieldDescriptionAttribute()
    {
        return $this->{static::field_description()};
    }
}
