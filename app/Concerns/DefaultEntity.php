<?php

namespace App\Concerns;

trait DefaultEntity
{
    public static function getModel()
    {
        return new static;
    }

    public static function getTableName()
    {
        return static::getModel()->getTable();
    }

    public static function field_key()
    {
        return static::getModel()->getKeyName();
    }

    public function getFieldKeyAttribute()
    {
        return $this->{$this->field_key()};
    }

    public static function field_primary()
    {
        return static::field_key();
    }

    public function getFieldPrimaryAttribute()
    {
        return $this->{$this->field_primary()};
    }

    public static function field_name()
    {
        return static::field_key();
    }

    public function getFieldNameAttribute()
    {
        return $this->{static::field_name()};
    }
}
