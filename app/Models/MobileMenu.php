<?php

namespace App\Models;

use App\Properties\MobileMenuEntity;

class MobileMenu extends BaseModel
{
    use MobileMenuEntity;

    protected $table = 'mobile_menu';

    protected $primaryKey = 'mobile_menu_id';

    public $timestamps = false;

    public $incrementing = true;

    protected $fillable = [
        'mobile_menu_nama',
        'mobile_menu_code',
        'mobile_menu_urut',
        'mobile_menu_aktif',
        'mobile_menu_deskripsi',
    ];

    public static $filterColumns = ['mobile_menu_nama', 'mobile_menu_code'];

    public static $sortColumns = ['mobile_menu_nama', 'mobile_menu_code', 'mobile_menu_urut', 'mobile_menu_aktif'];

    protected function casts(): array
    {
        return [
            'mobile_menu_id' => 'integer',
            'mobile_menu_urut' => 'integer',
            'mobile_menu_aktif' => 'boolean',
        ];
    }

    public static function field_name(): string
    {
        return 'mobile_menu_nama';
    }

    public function rules(): array
    {
        return [
            'mobile_menu_nama' => 'required|string|max:255',
            'mobile_menu_code' => 'nullable|string|max:50',
            'mobile_menu_urut' => 'nullable|integer|min:0',
            'mobile_menu_aktif' => 'nullable|boolean',
            'mobile_menu_deskripsi' => 'nullable|string',
        ];
    }

    public function hasUsers()
    {
        return $this->belongsToMany(User::class, 'mobile_menu_dan_user', 'mobile_menu_id', 'user_id');
    }
}
