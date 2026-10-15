<?php

namespace App\Dao\Entities;

use App\Dao\Enums\ProcessType;

trait ViewLogEntity
{
    public static function field_primary()
    {
        return 'view_log_rfid';
    }

    public function getFieldPrimaryAttribute()
    {
        return $this->{$this->field_primary()};
    }

    public static function field_user()
    {
        return 'view_log_user';
    }

    public function getFieldUserAttribute()
    {
        return $this->{$this->field_user()};
    }

    public static function field_status()
    {
        return 'view_log_status';
    }

    public function getFieldStatusAttribute()
    {
        return $this->{$this->field_status()};
    }

    public function getFieldStatusNameAttribute()
    {
        return ProcessType::getDescription($this->{$this->field_status()});
    }

    public static function field_waktu()
    {
        return 'view_log_waktu';
    }

    public function getFieldWaktuAttribute()
    {
        return $this->{$this->field_waktu()};
    }
}
