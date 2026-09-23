<?php

namespace App\Properties;

trait ActivityEntity
{
    public static function field_id()
    {
        return 'id';
    }

    public function getFieldIdAttribute()
    {
        return $this->{static::field_id()};
    }

    public static function field_log_name()
    {
        return 'log_name';
    }

    public function getFieldLogNameAttribute()
    {
        return $this->{static::field_log_name()};
    }

    public static function field_description()
    {
        return 'description';
    }

    public function getFieldDescriptionAttribute()
    {
        return $this->{static::field_description()};
    }

    public static function field_event()
    {
        return 'event';
    }

    public function getFieldEventAttribute()
    {
        return $this->{static::field_event()};
    }

    public static function field_subject_type()
    {
        return 'subject_type';
    }

    public function getFieldSubjectTypeAttribute()
    {
        return $this->{static::field_subject_type()};
    }

    public static function field_subject_id()
    {
        return 'subject_id';
    }

    public function getFieldSubjectIdAttribute()
    {
        return $this->{static::field_subject_id()};
    }

    public static function field_causer_type()
    {
        return 'causer_type';
    }

    public function getFieldCauserTypeAttribute()
    {
        return $this->{static::field_causer_type()};
    }

    public static function field_causer_id()
    {
        return 'causer_id';
    }

    public function getFieldCauserIdAttribute()
    {
        return $this->{static::field_causer_id()};
    }

    public static function field_created_at()
    {
        return 'created_at';
    }

    public function getFieldCreatedAtAttribute()
    {
        return $this->{static::field_created_at()};
    }

    public static function field_attribute_changes()
    {
        return 'attribute_changes';
    }

    public function getFieldAttributeChangesAttribute()
    {
        return $this->{static::field_attribute_changes()};
    }
}
