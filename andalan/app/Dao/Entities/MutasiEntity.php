<?php

namespace App\Dao\Entities;

trait MutasiEntity
{
    public static function field_primary()
    {
        return 'mutasi_id';
    }

    public function getFieldPrimaryAttribute()
    {
        return $this->{$this->field_primary()};
    }

    public static function field_name()
    {
        return 'mutasi_nama';
    }

    public function getFieldNameAttribute()
    {
        return $this->{$this->field_name()};
    }

    public static function field_description()
    {
        return 'mutasi_nama';
    }

    public function getFieldDescriptionAttribute()
    {
        return $this->{$this->field_description()};
    }

    public static function field_tanggal()
    {
        return 'mutasi_tanggal';
    }

    public function getFieldTanggalAttribute()
    {
        return $this->{$this->field_tanggal()};
    }

    public static function field_rs_id()
    {
        return 'mutasi_rs_id';
    }

    public function getFieldRsIdAttribute()
    {
        return $this->{$this->field_rs_id()};
    }

    public static function field_rs_nama()
    {
        return 'mutasi_rs_nama';
    }

    public function getFieldRsNamaAttribute()
    {
        return $this->{$this->field_rs_nama()};
    }

    public static function field_linen_id()
    {
        return 'mutasi_linen_id';
    }

    public function getFieldLinenIdAttribute()
    {
        return $this->{$this->field_linen_id()};
    }

    public static function field_linen_nama()
    {
        return 'mutasi_linen_nama';
    }

    public function getFieldLinenNamaAttribute()
    {
        return $this->{$this->field_linen_nama()};
    }

    public static function field_kotor()
    {
        return 'mutasi_kotor';
    }

    public function getFieldKotorAttribute()
    {
        return $this->{$this->field_kotor()};
    }

    public static function field_bersih()
    {
        return 'mutasi_bersih';
    }

    public function getFieldBersihAttribute()
    {
        return $this->{$this->field_bersih()};
    }

    public static function field_plus()
    {
        return 'mutasi_plus';
    }

    public function getFieldPlusAttribute()
    {
        return $this->{$this->field_plus()};
    }

    public static function field_minus()
    {
        return 'mutasi_minus';
    }

    public function getFieldMinusAttribute()
    {
        return $this->{$this->field_minus()};
    }

    public static function field_register()
    {
        return 'mutasi_register';
    }

    public function getFieldRegisterAttribute()
    {
        return $this->{$this->field_register()};
    }

    public static function field_saldo_awal()
    {
        return 'mutasi_saldo_awal';
    }

    public function getFieldSaldoAwalAttribute()
    {
        return $this->{$this->field_saldo_awal()};
    }

    public static function field_saldo_akhir()
    {
        return 'mutasi_saldo_akhir';
    }

    public function getFieldSaldoAkhirAttribute()
    {
        return $this->{$this->field_saldo_akhir()};
    }
}
