<?php

namespace App\Models;

use App\Properties\OpnameEntity;

class Opname extends BaseModel
{
    use OpnameEntity;

    protected $table = 'opname';

    protected $primaryKey = 'opname_id';

    public $timestamps = false;

    public $incrementing = true;

    protected $fillable = [
        'opname_id',
        'opname_nama',
        'opname_mulai',
        'opname_selesai',
        'opname_id_rs',
        'opname_status',
        'opname_capture',
        'opname_created_at',
        'opname_created_by',
        'opname_updated_at',
        'opname_updated_by',
    ];

    const CREATED_AT = 'opname_created_at';
    const UPDATED_AT = 'opname_updated_at';

    public static $filterColumns = [
        'opname_nama' => 'Nama',
        'opname_mulai' => 'Mulai',
        'opname_selesai' => 'Selesai',
        'opname_status' => 'Status',
    ];

    public static $sortColumns = ['opname_nama', 'opname_mulai', 'opname_status', 'opname_created_at'];

    protected function casts(): array
    {
        return [
            'opname_id' => 'integer',
            'opname_id_rs' => 'integer',
            'opname_status' => 'integer',
            'opname_mulai' => 'date',
            'opname_selesai' => 'date',
            'opname_capture' => 'datetime',
            'opname_created_at' => 'datetime',
            'opname_updated_at' => 'datetime',
        ];
    }

    public static function field_name(): string { return 'opname_nama'; }

    public function rules(): array
    {
        return [
            'opname_nama' => 'nullable|string|max:255',
            'opname_mulai' => 'nullable|date',
            'opname_selesai' => 'nullable|date|after_or_equal:opname_mulai',
            'opname_id_rs' => 'nullable|integer|exists:rs,rs_id',
            'opname_status' => 'nullable|integer',
        ];
    }

    public function hasRs()
    {
        return $this->hasOne(Rs::class, 'rs_id', 'opname_id_rs');
    }

    public function hasDetail()
    {
        return $this->hasMany(OpnameDetail::class, 'opname_detail_id_opname', 'opname_id');
    }

    protected static function booted(): void
    {
        static::creating(function (self $m) {
            $m->opname_created_by ??= auth()->id();
            $m->opname_created_at ??= now();
        });
        static::updating(function (self $m) {
            $m->opname_updated_by = auth()->id();
            $m->opname_updated_at = now();
        });
    }
}
