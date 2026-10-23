<?php

namespace App\Dao\Entities;

trait PendingEntity
{
    public static function field_primary()
    {
        return 'pending_id';
    }

    public function getFieldPrimaryAttribute()
    {
        return $this->{$this->field_primary()};
    }

    public static function field_name()
    {
        return 'pending_rfid';
    }

    public function getFieldNameAttribute()
    {
        return $this->{$this->field_name()};
    }

    public static function field_status()
    {
        return 'pending_status';
    }

    public function getFieldStatusAttribute()
    {
        return $this->{$this->field_status()};
    }
}
