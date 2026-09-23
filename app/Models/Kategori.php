<?php

namespace App\Models;

use App\Properties\KategoriEntity;

class Kategori extends BaseModel
{
    use KategoriEntity;

    protected $table = 'kategori';

    protected $primaryKey = 'kategori_id';

    public $timestamps = false;

    public $incrementing = true;

    protected $fillable = [
        'kategori_nama',
        'kategori_deskripsi',
    ];

    public static $filterColumns = ['kategori_nama'];

    public static $sortColumns = ['kategori_nama', 'kategori_deskripsi'];

    protected function casts(): array
    {
        return [
            'kategori_id' => 'integer',
        ];
    }

    public static function field_name(): string
    {
        return 'kategori_nama';
    }

    public function rules(): array
    {
        return [
            'kategori_nama' => 'required|string|max:255',
            'kategori_deskripsi' => 'nullable|string',
        ];
    }
}
