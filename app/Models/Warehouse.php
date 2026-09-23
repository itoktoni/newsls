<?php

namespace App\Models;

class Warehouse extends BaseModel
{
    protected $table = 'warehouse';

    protected $primaryKey = 'warehouse_id';

    public $timestamps = false;

    protected $fillable = [
        'warehouse_nama',
    ];

    public static $filterColumns = ['warehouse_nama'];

    public static $sortColumns = ['warehouse_nama'];

    protected function casts(): array
    {
        return [
            'warehouse_id' => 'integer',
        ];
    }

    public static function field_name(): string
    {
        return 'warehouse_nama';
    }

    public function rules(): array
    {
        return [
            'warehouse_nama' => 'required|string|max:255',
        ];
    }

    public static function utamaId(): int
    {
        return (int) env('GUDANG_UTAMA_ID', 1);
    }

    public function hasStock()
    {
        return $this->hasMany(Outstanding::class, 'outstanding_id_warehouse', 'warehouse_id');
    }
}
