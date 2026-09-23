<?php

namespace App\Models;

use App\Properties\JenisLinenEntity;

class JenisLinen extends BaseModel
{
    use JenisLinenEntity;

    protected $table = 'jenis_linen';

    protected $primaryKey = 'jenis_id';

    public $timestamps = false;

    public $incrementing = true;

    protected $fillable = [
        'jenis_id_kategori',
        'jenis_nama',
        'jenis_deskripsi',
        'jenis_id_rs',
        'jenis_gambar',
        'jenis_berat',
    ];

    public static $filterColumns = ['jenis_nama', 'jenis_id_kategori', 'jenis_id_rs'];

    public static $sortColumns = ['jenis_nama', 'jenis_berat', 'jenis_deskripsi'];

    protected function casts(): array
    {
        return [
            'jenis_id' => 'integer',
            'jenis_id_kategori' => 'integer',
            'jenis_id_rs' => 'integer',
            'jenis_berat' => 'float',
        ];
    }

    public static function field_name(): string
    {
        return 'jenis_nama';
    }

    public function rules(): array
    {
        return [
            'jenis_id_kategori' => 'nullable|integer',
            'jenis_nama' => 'required|string|max:255',
            'jenis_deskripsi' => 'nullable|string',
            'jenis_id_rs' => 'nullable|integer',
            'jenis_gambar' => 'nullable|string|max:255',
            'jenis_berat' => 'nullable|numeric|min:0',
        ];
    }

    public function hasCategory()
    {
        return $this->hasOne(Kategori::class, 'kategori_id', 'jenis_id_kategori');
    }

    public function hasRs()
    {
        return $this->hasOne(Rs::class, 'rs_id', 'jenis_id_rs');
    }
}
