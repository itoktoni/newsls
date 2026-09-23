<?php

namespace App\Dao\Entities;

trait FiltersEntity
{
    public static function field_primary()
    {
        return 'filter_id';
    }

    public function getFieldPrimaryAttribute()
    {
        return $this->{$this->field_primary()};
    }

    public static function field_name()
    {
        return 'filter_code';
    }

    public function getFieldNameAttribute()
    {
        return $this->{$this->field_name()};
    }

    public static function field_table()
    {
        return 'filter_table';
    }

    public function getFieldTableAttribute()
    {
        return $this->{$this->field_table()};
    }

    public static function field_module()
    {
        return 'filter_module';
    }

    public function getFieldModuleAttribute()
    {
        return $this->{$this->field_module()};
    }

    public static function field_from_user()
    {
        return 'filter_from_user';
    }

    public function getFieldFromUserAttribute()
    {
        return $this->{$this->field_from_user()};
    }

    public static function field_field()
    {
        return 'filter_field';
    }

    public function getFieldFieldAttribute()
    {
        return $this->{$this->field_field()};
    }

    public static function field_function()
    {
        return 'filter_function';
    }

    public function getFieldFunctionAttribute()
    {
        return $this->{$this->field_function()};
    }

    public static function field_operator()
    {
        return 'filter_operator';
    }

    public function getFieldOperatorAttribute()
    {
        return $this->{$this->field_operator()};
    }

    public static function field_value()
    {
        return 'filter_value';
    }

    public function getFieldValueAttribute()
    {
        return $this->{$this->field_value()};
    }
}
