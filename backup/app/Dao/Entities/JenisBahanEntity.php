<?php

namespace App\Dao\Entities;

trait JenisBahanEntity
{
    public static function field_primary()
    {
        return 'bahan_id';
    }

    public function getFieldPrimaryAttribute()
    {
        return $this->{$this->field_primary()};
    }

    public static function field_name()
    {
        return 'bahan_nama';
    }

    public function getFieldNameAttribute()
    {
        return $this->{$this->field_name()};
    }

    public static function field_description()
    {
        return 'bahan_deskripsi';
    }

    public function getFieldDescriptionAttribute()
    {
        return $this->{$this->field_description()};
    }
}
