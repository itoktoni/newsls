<?php

namespace App\Dao\Entities;

trait SystemMenuEntity
{
    public static function field_primary()
    {
        return 'system_menu_code';
    }

    public function getFieldPrimaryAttribute()
    {
        return $this->{$this->field_primary()};
    }

    public static function field_name()
    {
        return 'system_menu_name';
    }

    public function getFieldNameAttribute()
    {
        return $this->{$this->field_name()};
    }

    public static function field_description()
    {
        return 'system_menu_description';
    }

    public function getFieldDescriptionAttribute()
    {
        return $this->{$this->field_description()};
    }

    public static function field_enable()
    {
        return 'system_menu_enable';
    }

    public function getFieldEnableAttribute()
    {
        return $this->{$this->field_enable()};
    }

    public static function field_sort()
    {
        return 'system_menu_sort';
    }

    public function getFieldSortAttribute()
    {
        return $this->{self::field_sort()};
    }

    public static function field_url()
    {
        return 'system_menu_url';
    }

    public function getFieldUrlAttribute()
    {
        return $this->{self::field_url()};
    }

    public static function field_controller()
    {
        return 'system_menu_controller';
    }

    public function getFieldControllerAttribute()
    {
        return $this->{self::field_controller()};
    }

    public static function field_action()
    {
        return 'system_menu_action';
    }

    public function getFieldActionAttribute()
    {
        return $this->{self::field_action()};
    }

    public static function field_type()
    {
        return 'system_menu_type';
    }

    public function getFieldTypeAttribute()
    {
        return $this->{self::field_type()};
    }

    public static function field_can_delete()
    {
        return 'system_menu_can_delete';
    }

    public function getFieldCanDeleteAttribute()
    {
        return $this->{self::field_can_delete()};
    }
}
