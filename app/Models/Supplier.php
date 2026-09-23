<?php

namespace App\Models;

use App\Properties\SupplierEntity;

class Supplier extends BaseModel
{
    use SupplierEntity;

    protected $table = 'supplier';

    protected $primaryKey = 'supplier_id';

    public $timestamps = false;

    public $incrementing = true;

    protected $fillable = [
        'supplier_nama',
        'supplier_alamat',
        'supplier_phone',
        'supplier_kontak',
        'supplier_email',
    ];

    public static $filterColumns = ['supplier_nama', 'supplier_email'];

    public static $sortColumns = ['supplier_nama', 'supplier_kontak', 'supplier_email', 'supplier_phone'];

    protected function casts(): array
    {
        return [
            'supplier_id' => 'integer',
        ];
    }

    public static function field_name(): string
    {
        return 'supplier_nama';
    }

    public function rules(): array
    {
        return [
            'supplier_nama' => 'required|string|max:255',
            'supplier_alamat' => 'nullable|string',
            'supplier_phone' => 'nullable|string|max:50',
            'supplier_kontak' => 'nullable|string|max:255',
            'supplier_email' => 'nullable|email|max:255',
        ];
    }
}
