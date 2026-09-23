<?php

namespace App\Models;

use App\Properties\RuanganEntity;

class Ruangan extends BaseModel
{
    use RuanganEntity;

    protected $table = 'ruangan';

    protected $primaryKey = 'ruangan_id';

    public $timestamps = false;

    public $incrementing = true;

    protected $fillable = [
        'ruangan_nama',
        'ruangan_deskripsi',
        'ruangan_code',
    ];

    public static $filterColumns = ['ruangan_nama', 'ruangan_code'];

    public static $sortColumns = ['ruangan_nama', 'ruangan_code', 'ruangan_deskripsi'];

    protected function casts(): array
    {
        return [
            'ruangan_id' => 'integer',
        ];
    }

    public static function field_name(): string
    {
        return 'ruangan_nama';
    }

    public function rules(): array
    {
        return [
            'ruangan_nama' => 'required|string|max:255',
            'ruangan_deskripsi' => 'nullable|string',
            'ruangan_code' => 'nullable|string|max:255',
        ];
    }
}
