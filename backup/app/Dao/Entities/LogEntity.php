<?php

namespace App\Dao\Entities;

use App\Dao\Enums\ProcessType;

trait LogEntity
{
    public static function field_primary()
    {
        return 'log_id';
    }

    public function getFieldPrimaryAttribute()
    {
        return $this->{$this->field_primary()};
    }

    public static function field_name()
    {
        return 'log_rfid';
    }

    public function getFieldNameAttribute()
    {
        return $this->{$this->field_name()};
    }

    public static function field_rfid()
    {
        return 'log_rfid';
    }

    public function getFieldRfidAttribute()
    {
        return $this->{$this->field_rfid()};
    }

    public static function field_rs()
    {
        return 'log_id_rs';
    }

    public function getFieldRsAttribute()
    {
        return $this->{$this->field_rs()};
    }

    public static function field_jenis()
    {
        return 'log_id_jenis';
    }

    public function getFieldJenisAttribute()
    {
        return $this->{$this->field_jenis()};
    }

    public static function field_ruangan()
    {
        return 'log_id_ruangan';
    }

    public function getFieldRuanganAttribute()
    {
        return $this->{$this->field_ruangan()};
    }

    public static function field_user()
    {
        return 'log_user';
    }

    public function getFieldUserAttribute()
    {
        return $this->{$this->field_user()};
    }

    public static function field_in()
    {
        return 'log_in';
    }

    public function getFieldInAttribute()
    {
        return $this->{$this->field_in()};
    }

    public static function field_out()
    {
        return 'log_out';
    }

    public function getFieldOutAttribute()
    {
        return $this->{$this->field_out()};
    }

    public static function field_tanggal()
    {
        return 'log_tanggal';
    }

    public function getFieldTanggalAttribute()
    {
        return $this->{$this->field_tanggal()};
    }

    public static function field_check()
    {
        return 'log_check';
    }

    public function getFieldCheckAttribute()
    {
        return $this->{$this->field_check()};
    }
}
