<?php

namespace App\Dao\Entities;

use App\Dao\Enums\RoleLevel;

trait SystemRoleEntity
{
    public static function field_primary()
    {
        return 'system_role_code';
    }

    public function getFieldPrimaryAttribute()
    {
        return $this->{$this->field_primary()};
    }

    public static function field_name()
    {
        return 'system_role_name';
    }

    public function getFieldNameAttribute()
    {
        return $this->{$this->field_name()};
    }

    public static function field_description()
    {
        return 'system_role_description';
    }

    public function getFieldDescriptionAttribute()
    {
        return $this->{$this->field_description()};
    }

    public static function field_level()
    {
        return 'system_role_level';
    }

    public function getFieldLevelNameAttribute()
    {
        return RoleLevel::getDescription($this->{$this->field_level()});
    }

    public function getFieldLevelAttribute()
    {
        return $this->{$this->field_level()};
    }
}
