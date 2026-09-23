<?php

namespace App\Models;

use App\Properties\TransaksiEntity;

class Transaksi extends BaseModel
{
    use TransaksiEntity;

    protected $table = 'transaksi';

    protected $primaryKey = 'transaksi_id';

    public $timestamps = false;

    public $incrementing = true;

    protected $fillable = [
        'transaksi_key',
        'transaksi_rfid',
        'transaksi_rs_ori',
        'transaksi_rs_scan',
        'transaksi_beda_rs',
        'transaksi_id_ruangan',
        'transaksi_status',
        'transaksi_created_at',
        'transaksi_created_by',
        'transaksi_updated_at',
        'transaksi_updated_by',
    ];

    const CREATED_AT = 'transaksi_created_at';

    const UPDATED_AT = 'transaksi_updated_at';

    public static $filterColumns = ['transaksi_key', 'transaksi_rfid', 'transaksi_status'];

    public static $sortColumns = ['transaksi_key', 'transaksi_rfid', 'transaksi_status', 'transaksi_created_at'];

    protected function casts(): array
    {
        return [
            'transaksi_id' => 'integer',
            'transaksi_rs_ori' => 'integer',
            'transaksi_rs_scan' => 'integer',
            'transaksi_created_at' => 'datetime',
            'transaksi_updated_at' => 'datetime',
        ];
    }

    public static function field_name(): string
    {
        return 'transaksi_key';
    }

    public function rules(): array
    {
        return [
            'transaksi_key' => 'required|string|max:255',
            'transaksi_rfid' => 'required|string|max:255',
            'transaksi_status' => 'required|string|max:20',
        ];
    }

    public function hasDetail()
    {
        return $this->hasOne(DetailLinen::class, 'detail_rfid', 'transaksi_rfid');
    }

    public function hasRsOri()
    {
        return $this->hasOne(Rs::class, 'rs_id', 'transaksi_rs_ori');
    }

    public function hasRsScan()
    {
        return $this->hasOne(Rs::class, 'rs_id', 'transaksi_rs_scan');
    }

    public function hasRuangan()
    {
        return $this->hasOne(Ruangan::class, 'ruangan_id', 'transaksi_id_ruangan');
    }
}
