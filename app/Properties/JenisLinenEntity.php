<?php

namespace App\Properties;

trait JenisLinenEntity
{
    public static function field_description()
    {
        return 'jenis_deskripsi';
    }

    public function getFieldDescriptionAttribute()
    {
        return $this->{static::field_description()};
    }

    public static function field_rs_id()
    {
        return 'jenis_id_rs';
    }

    public function getFieldRsIdAttribute()
    {
        return $this->{static::field_rs_id()};
    }

    public static function field_category_id()
    {
        return 'jenis_id_kategori';
    }

    public function getFieldCategoryIdAttribute()
    {
        return $this->{static::field_category_id()};
    }

    public static function field_weight()
    {
        return 'jenis_berat';
    }

    public function getFieldWeightAttribute()
    {
        return $this->{static::field_weight()} ?? 0;
    }

    public static function field_image()
    {
        return 'jenis_gambar';
    }

    public function getFieldImageAttribute()
    {
        return $this->{static::field_image()};
    }

    public function getGambarUrlAttribute(): string
    {
        return fileUrl($this->{static::field_image()});
    }
}
