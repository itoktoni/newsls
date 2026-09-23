<?php

namespace App\Dao\Entities;

trait ViewTotalJenisEntity
{
    public static function field_primary()
    {
        return 'view_jenis_id';
    }

    public function getFieldPrimaryAttribute()
    {
        return $this->{$this->field_primary()};
    }

    public static function field_total()
    {
        return 'view_jenis_total';
    }

    public function getFieldNameAttribute()
    {
        return $this->{$this->field_total()};
    }

    public static function field_rs_id()
    {
        return 'view_rs_id';
    }

    public function getFieldRsIdAttribute()
    {
        return $this->{$this->field_rs_id()};
    }

    public static function field_parstok()
    {
        return 'view_parstok';
    }

    public function getFieldParstokAttribute()
    {
        return $this->{$this->field_parstok()};
    }
}
