<?php

namespace App\Properties;

use App\Enums\CuciEnum;
use App\Enums\LinenStatusEnum;
use App\Enums\RegisterEnum;

trait DetailLinenEntity
{
    public static function field_description()
    {
        return 'detail_deskripsi';
    }

    public function getFieldDescriptionAttribute()
    {
        return $this->{static::field_description()};
    }

    public static function field_rs_id()
    {
        return 'detail_id_rs';
    }

    public function getFieldRsIdAttribute()
    {
        return $this->{static::field_rs_id()};
    }

    public static function field_ruangan_id()
    {
        return 'detail_id_ruangan';
    }

    public function getFieldRuanganIdAttribute()
    {
        return $this->{static::field_ruangan_id()};
    }

    public static function field_jenis_id()
    {
        return 'detail_id_jenis';
    }

    public function getFieldJenisIdAttribute()
    {
        return $this->{static::field_jenis_id()};
    }

    public static function field_bahan_id()
    {
        return 'detail_id_bahan';
    }

    public function getFieldBahanIdAttribute()
    {
        return $this->{static::field_bahan_id()};
    }

    public static function field_supplier_id()
    {
        return 'detail_id_supplier';
    }

    public function getFieldSupplierIdAttribute()
    {
        return $this->{static::field_supplier_id()};
    }

    public static function field_status_cuci()
    {
        return 'detail_status_cuci';
    }

    public function getFieldStatusCuciAttribute()
    {
        return $this->{static::field_status_cuci()};
    }

    public function getFieldStatusCuciNameAttribute(): string
    {
        return CuciEnum::getDescription($this->getFieldStatusCuciAttribute());
    }

    public static function field_status_linen()
    {
        return 'detail_status_linen';
    }

    public function getFieldStatusLinenAttribute()
    {
        return $this->{static::field_status_linen()};
    }

    public function getFieldStatusLinenNameAttribute(): string
    {
        return LinenStatusEnum::getDescription($this->getFieldStatusLinenAttribute());
    }

    public static function field_status_register()
    {
        return 'detail_status_register';
    }

    public function getFieldStatusRegisterAttribute()
    {
        return $this->{static::field_status_register()};
    }

    public function getFieldStatusRegisterNameAttribute(): string
    {
        return RegisterEnum::getDescription($this->getFieldStatusRegisterAttribute());
    }

    public static function field_status_kepemilikan()
    {
        return 'detail_status_kepemilikan';
    }

    public function getFieldStatusKepemilikanAttribute()
    {
        return $this->{static::field_status_kepemilikan()};
    }

    public static function field_created_at()
    {
        return 'detail_created_at';
    }

    public static function field_created_by()
    {
        return 'detail_created_by';
    }

    public static function field_updated_at()
    {
        return 'detail_updated_at';
    }

    public static function field_updated_by()
    {
        return 'detail_updated_by';
    }

    public static function field_cek()
    {
        return 'detail_tgl_cek';
    }

    public function getFieldCekAttribute()
    {
        return $this->{static::field_cek()};
    }

    public static function field_report()
    {
        return 'detail_report';
    }

    public function getFieldReportAttribute()
    {
        return $this->{static::field_report()};
    }
}
