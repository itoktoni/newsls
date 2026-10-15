<?php

namespace App\Dao\Entities;

trait SystemPermisionEntity
{
    public static function field_primary()
    {
        return 'system_permision_id';
    }

    public function getFieldPrimaryAttribute()
    {
        return $this->{$this->field_primary()};
    }

    public static function field_role()
    {
        return 'system_permision_role';
    }

    public function getFieldRoleAttribute()
    {
        return $this->{$this->field_role()};
    }

    public static function field_code()
    {
        return 'system_permision_code';
    }

    public function getFieldCodeAttribute()
    {
        return $this->{$this->field_code()};
    }

    public static function field_user()
    {
        return 'system_permision_user';
    }

    public function getFieldUserAttribute()
    {
        return $this->{$this->field_user()};
    }

    public static function field_level()
    {
        return 'system_permision_level';
    }

    public function getFieldLevelAttribute()
    {
        return $this->{self::field_level()};
    }

    public static function field_module()
    {
        return 'system_permision_module';
    }

    public function getFieldModuleAttribute()
    {
        return $this->{self::field_module()};
    }

    public static function field_controller()
    {
        return 'system_permision_controller';
    }

    public function getFieldControllerAttribute()
    {
        return $this->{self::field_controller()};
    }
}
