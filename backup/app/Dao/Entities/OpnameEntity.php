<?php

namespace App\Dao\Entities;

use App\Dao\Enums\OpnameType;
use App\Dao\Models\Rs;

trait OpnameEntity
{
    public static function field_primary()
    {
        return 'opname_id';
    }

    public function getFieldPrimaryAttribute()
    {
        return $this->{$this->field_primary()};
    }

    public static function field_name()
    {
        return 'opname_nama';
    }

    public function getFieldNameAttribute()
    {
        return $this->{$this->field_name()};
    }

    public static function field_start()
    {
        return 'opname_mulai';
    }

    public function getFieldStartAttribute()
    {
        return $this->{$this->field_start()};
    }

    public static function field_end()
    {
        return 'opname_selesai';
    }

    public function getFieldEndAttribute()
    {
        return $this->{$this->field_end()};
    }

    public static function field_created_at()
    {
        return 'opname_created_at';
    }

    public function getFieldCreatedAtAttribute()
    {
        return $this->{$this->field_created_at()};
    }

    public static function field_updated_at()
    {
        return 'opname_updated_at';
    }

    public function getFieldUpdatedAtAttribute()
    {
        return $this->{$this->field_updated_at()};
    }

    public static function field_capture()
    {
        return 'opname_capture';
    }

    public function getFieldCaptureAttribute()
    {
        return $this->{$this->field_capture()};
    }

    public static function field_rs_id()
    {
        return 'opname_id_rs';
    }

    public function getFieldRsIdAttribute()
    {
        return $this->{$this->field_rs_id()};
    }

    public function getFieldRsNameAttribute()
    {
        return $this->{Rs::field_name()};
    }

    public static function field_status()
    {
        return 'opname_status';
    }

    public function getFieldStatusAttribute()
    {
        return $this->{$this->field_status()};
    }

    public function getFieldStatusNameAttribute()
    {
        return $this->getFieldStatusAttribute() ? OpnameType::getDescription($this->getFieldStatusAttribute()) : 'Draft';
    }
}
