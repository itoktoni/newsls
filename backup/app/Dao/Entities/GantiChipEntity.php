<?php

namespace App\Dao\Entities;

trait GantiChipEntity
{
    public static function field_primary()
    {
        return 'ganti_id';
    }

    public function getFieldPrimaryAttribute()
    {
        return $this->{$this->field_primary()};
    }

    public static function field_lama()
    {
        return 'ganti_rfid_lama';
    }

    public function getFieldLamaAttribute()
    {
        return $this->{$this->field_lama()};
    }

    public static function field_baru()
    {
        return 'ganti_rfid_baru';
    }

    public function getFieldBaruAttribute()
    {
        return $this->{$this->field_baru()};
    }

    public static function field_tanggal()
    {
        return 'ganti_tanggal';
    }

    public function getFieldTanggalAttribute()
    {
        return $this->{$this->field_tanggal()};
    }

    public static function field_by()
    {
        return 'ganti_by';
    }

    public function getFieldByAttribute()
    {
        return $this->{$this->field_by()};
    }

    public static function field_keterangan()
    {
        return 'ganti_keterangan';
    }

    public function getFieldKeteranganAttribute()
    {
        return $this->{$this->field_keterangan()};
    }
}
