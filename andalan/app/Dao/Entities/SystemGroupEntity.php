<?php

namespace App\Dao\Entities;

trait SystemGroupEntity
{
    public static function field_primary()
    {
        return 'system_group_code';
    }

    public function getFieldPrimaryAttribute()
    {
        return $this->{$this->field_primary()};
    }

    public static function field_name()
    {
        return 'system_group_name';
    }

    public function getFieldNameAttribute()
    {
        return $this->{$this->field_name()};
    }

    public static function field_description()
    {
        return 'system_group_description';
    }

    public function getFieldDescriptionAttribute()
    {
        return $this->{$this->field_description()};
    }

    public static function field_enable()
    {
        return 'system_group_enable';
    }

    public function getFieldEnableAttribute()
    {
        return $this->{$this->field_enable()};
    }

    public static function field_sort()
    {
        return 'system_group_sort';
    }

    public function getFieldSortAttribute()
    {
        return $this->{self::field_sort()};
    }

    public static function field_icon()
    {
        return 'system_group_icon';
    }

    public function getFieldIconAttribute()
    {
        return $this->{self::field_icon()};
    }

    public static function field_url()
    {
        return 'system_group_url';
    }

    public function getFieldUrlAttribute()
    {
        return $this->{self::field_url()};
    }
}
