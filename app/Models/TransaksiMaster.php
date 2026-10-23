<?php

namespace App\Models;

class TransaksiMaster extends BaseModel
{
    protected $table = 'transaksi_master';

    protected $primaryKey = 'master_id';

    public $timestamps = false;

    public $incrementing = true;

    const CREATED_AT = 'master_created_at';

    const UPDATED_AT = 'master_updated_at';

    protected $fillable = [
        'master_kode',
        'master_rs_id',
        'master_status',
        'master_tanggal',
        'master_qty',
        'master_created_by',
        'master_updated_by',
        'master_created_at',
        'master_updated_at',
    ];

    public static $filterColumns = ['master_kode', 'master_status'];

    public static $sortColumns = ['master_kode', 'master_status', 'master_tanggal', 'master_created_at'];

    protected function casts(): array
    {
        return [
            'master_id' => 'integer',
            'master_rs_id' => 'integer',
            'master_qty' => 'float',
            'master_tanggal' => 'date',
            'master_created_at' => 'datetime',
            'master_updated_at' => 'datetime',
        ];
    }

    public static function field_name(): string
    {
        return 'master_kode';
    }

    public function rules(): array
    {
        return [
            'master_kode' => 'required|string|max:255',
            'master_rs_id' => 'nullable|integer|exists:rs,rs_id',
            'master_status' => 'required|string|max:20',
            'master_tanggal' => 'nullable|date',
            'master_qty' => 'nullable|numeric|min:0',
        ];
    }

    protected static function booted(): void
    {
        // Hapus master = hapus seluruh detail RFID dalam key yang sama.
        static::deleting(function (TransaksiMaster $master) {
            Transaksi::where('transaksi_key', $master->master_kode)->delete();
        });
    }

    public function hasRs()
    {
        return $this->hasOne(Rs::class, 'rs_id', 'master_rs_id');
    }

    public function hasOperator()
    {
        return $this->hasOne(User::class, 'id', 'master_created_by');
    }

    public function hasDetails()
    {
        return $this->hasMany(Transaksi::class, 'transaksi_key', 'master_kode');
    }
}
