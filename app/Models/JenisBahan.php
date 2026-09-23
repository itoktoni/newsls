<?php

namespace App\Models;

use App\Properties\JenisBahanEntity;

class JenisBahan extends BaseModel
{
    use JenisBahanEntity;

    protected $table = 'jenis_bahan';

    protected $primaryKey = 'bahan_id';

    public $timestamps = false;

    public $incrementing = true;

    protected $fillable = [
        'bahan_nama',
        'bahan_deskripsi',
    ];

    public static $filterColumns = ['bahan_nama'];

    public static $sortColumns = ['bahan_nama', 'bahan_deskripsi'];

    protected function casts(): array
    {
        return [
            'bahan_id' => 'integer',
        ];
    }

    public static function field_name(): string
    {
        return 'bahan_nama';
    }

    public function rules(): array
    {
        return [
            'bahan_nama' => 'required|string|max:255',
            'bahan_deskripsi' => 'nullable|string',
        ];
    }
}
