<?php

namespace App\Models;

use App\Properties\OpnameDetailEntity;

class OpnameDetail extends BaseModel
{
    use OpnameDetailEntity;

    protected $table = 'opname_detail';

    protected $primaryKey = 'opname_detail_id';

    public $timestamps = false;

    public $incrementing = true;

    protected $fillable = [
        'opname_detail_id_opname',
        'opname_detail_code',
        'opname_detail_rfid',
        'opname_detail_waktu',
        'opname_detail_transaksi',
        'opname_detail_proses',
        'opname_detail_hilang',
        'opname_detail_ketemu',
        'opname_detail_created_at',
        'opname_detail_updated_at',
        'opname_detail_created_by',
        'opname_detail_updated_by',
        'opname_detail_register',
        'opname_detail_hilang_at',
        'opname_detail_pending_at',
        'opname_detail_scan_rs',
        'opname_detail_reff',
        'opname_detail_scan_by',
    ];

    public static $filterColumns = ['opname_detail_rfid', 'opname_detail_ketemu', 'opname_detail_transaksi'];
    public static $sortColumns = ['opname_detail_rfid', 'opname_detail_waktu'];

    protected function casts(): array
    {
        return [
            'opname_detail_id' => 'integer',
            'opname_detail_id_opname' => 'integer',
            'opname_detail_ketemu' => 'integer',
            'opname_detail_scan_rs' => 'integer',
            'opname_detail_waktu' => 'datetime',
            'opname_detail_created_at' => 'datetime',
        ];
    }

    public static function field_name(): string { return 'opname_detail_rfid'; }

    public function rules(): array
    {
        return [
            'opname_detail_rfid' => 'required|string|max:255',
            'opname_detail_id_opname' => 'required|integer|exists:opname,opname_id',
        ];
    }

    public function hasOpname()
    {
        return $this->hasOne(Opname::class, 'opname_id', 'opname_detail_id_opname');
    }

    public function hasView()
    {
        // join detail_linen via rfid for linen name
        return $this->hasOne(DetailLinen::class, 'detail_rfid', 'opname_detail_rfid');
    }
}
