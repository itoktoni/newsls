<?php

namespace App\Properties;

trait MobileMenuEntity
{
    public static function field_description()
    {
        return 'mobile_menu_deskripsi';
    }

    public function getFieldDescriptionAttribute()
    {
        return $this->{static::field_description()};
    }

    public static function field_code()
    {
        return 'mobile_menu_code';
    }

    public function getFieldCodeAttribute()
    {
        return $this->{static::field_code()};
    }
}
