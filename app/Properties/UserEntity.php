<?php

namespace App\Properties;

trait UserEntity
{
    public static function field_email()
    {
        return 'email';
    }

    public function getFieldEmailAttribute()
    {
        return $this->{static::field_email()};
    }
}
