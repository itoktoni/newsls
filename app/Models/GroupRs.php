<?php

namespace App\Models;

use App\Properties\GroupRsEntity;

class GroupRs extends BaseModel
{
    use GroupRsEntity;

    protected $table = 'group_rs';

    protected $primaryKey = 'group_rs_id';

    public $timestamps = false;

    public $incrementing = true;

    protected $fillable = [
        'group_rs_nama',
        'group_rs_code',
        'group_rs_deskripsi',
    ];

    public static $filterColumns = ['group_rs_code', 'group_rs_nama'];

    public static $sortColumns = ['group_rs_code', 'group_rs_name'];

    protected function casts(): array
    {
        return [
            'group_rs_id' => 'integer',
        ];
    }

    public static function field_name(): string
    {
        return 'group_rs_nama';
    }

    public function rules(): array
    {
        return [
            'group_rs_nama' => 'required|string|max:255',
            'group_rs_code' => 'nullable|string|max:255',
            'group_rs_deskripsi' => 'nullable|string',
        ];
    }

    public function hasRs()
    {
        return $this->hasMany(Rs::class, 'rs_id_group', 'group_rs_id');
    }
}
