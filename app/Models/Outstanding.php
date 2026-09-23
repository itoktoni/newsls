<?php

namespace App\Models;

class Outstanding extends BaseModel
{
    protected $table = 'outstanding';

    protected $primaryKey = 'outstanding_rfid';

    public $timestamps = false;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'outstanding_rfid',
        'outstanding_key',
        'outstanding_rs_ori',
        'outstanding_rs_scan',
        'outstanding_beda_rs',
        'outstanding_status_beda_rs',
        'outstanding_id_ruangan',
        'outstanding_id_warehouse',
        'outstanding_status_transaksi',
        'outstanding_status_process',
        'outstanding_status_proses',
        'outstanding_status_hilang',
        'outstanding_created_at',
        'outstanding_updated_at',
        'outstanding_created_by',
        'outstanding_updated_by',
        'outstanding_pending_created_at',
        'outstanding_pending_updated_at',
        'outstanding_hilang_created_at',
        'outstanding_hilang_updated_at',
    ];

    const CREATED_AT = 'outstanding_created_at';

    const UPDATED_AT = 'outstanding_updated_at';

    public static $filterColumns = ['outstanding_rfid', 'outstanding_key'];

    public static $sortColumns = ['outstanding_rfid', 'outstanding_key'];

    protected function casts(): array
    {
        return [
            'outstanding_created_at' => 'datetime',
            'outstanding_updated_at' => 'datetime',
        ];
    }

    public static function field_name(): string
    {
        return 'outstanding_rfid';
    }

    public function rules(): array
    {
        return [
            'outstanding_rfid' => 'required|string|max:255',
            'outstanding_key' => 'nullable|string|max:255',
        ];
    }
}
