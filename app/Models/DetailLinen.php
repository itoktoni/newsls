<?php

namespace App\Models;

use App\Enums\LinenStatusEnum;
use App\Enums\RegisterEnum;
use App\Properties\DetailLinenEntity;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class DetailLinen extends BaseModel
{
    use DetailLinenEntity, LogsActivity;

    protected $table = 'detail_linen';

    protected $primaryKey = 'detail_rfid';

    public $timestamps = true;

    public $incrementing = false;

    protected $keyType = 'string';

    const CREATED_AT = 'detail_created_at';

    const UPDATED_AT = 'detail_updated_at';

    protected $fillable = [
        'detail_id',
        'detail_rfid',
        'detail_id_rs',
        'detail_id_ruangan',
        'detail_id_jenis',
        'detail_id_bahan',
        'detail_id_supplier',
        'detail_deskripsi',
        'detail_status_cuci',
        'detail_status_register',
        'detail_status_kepemilikan',
        'detail_status_linen',
        'detail_total_rewash',
        'detail_total_reject',
        'detail_total_bersih',
        'detail_tgl_cek',
        'detail_report',
        'detail_created_by',
        'detail_updated_by',
    ];

    protected function casts(): array
    {
        return [
            'detail_rfid' => 'string',
            'detail_tgl_cek' => 'date',
            'detail_report' => 'date',
            'detail_total_rewash' => 'integer',
            'detail_total_reject' => 'integer',
            'detail_total_bersih' => 'integer',
        ];
    }

    // ponytail: whitelist kolom filter (dipakai getFields() untuk dropdown
    // quick-search + getData() untuk filters[] advanced). Hanya kolom real
    // detail_linen — dot-notation relasi dilarang karena getData() sudah
    // join hasRs/hasRuangan/hasJenis (double join = SQL error).
    public static $filterColumns = [
        'detail_rfid' => 'RFID',
        'detail_id_rs' => 'Dipakai RS',
        'detail_id_ruangan' => 'Ruangan',
        'detail_id_jenis' => 'Jenis Linen',
        'detail_id_bahan' => 'Bahan',
        'detail_id_supplier' => 'Supplier',
        'detail_status_kepemilikan' => 'Kepemilikan',
        'detail_status_register' => 'Status Register',
        'detail_status_cuci' => 'Status Cuci',
        'detail_status_linen' => 'Status Linen',
        'detail_total_bersih' => 'Total Bersih (Kotor)',
        'detail_total_reject' => 'Total Reject (Retur)',
        'detail_total_rewash' => 'Total Rewash',
        'detail_tgl_cek' => 'Tanggal Cek',
    ];

    public static function field_name(): string
    {
        return 'detail_rfid';
    }

    public function rules(): array
    {
        return [
            'detail_rfid' => 'required|string|max:255',
            'detail_id_rs' => 'nullable|integer',
            'detail_id_ruangan' => 'nullable|integer',
            'detail_id_jenis' => 'nullable|integer',
            'detail_id_bahan' => 'nullable|integer',
            'detail_id_supplier' => 'nullable|integer',
            'detail_deskripsi' => 'nullable|string',
            'detail_status_cuci' => 'nullable|string|max:50',
            'detail_status_register' => 'nullable|string|max:50',
            'detail_status_kepemilikan' => 'nullable|string|max:50',
            'detail_status_linen' => 'nullable|string|max:50',
            'detail_tgl_cek' => 'nullable|date',
            'detail_report' => 'nullable|date',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (self $model) {
            $model->detail_status_linen ??= LinenStatusEnum::REGISTER;
            // ponytail: kolom ini ada di tabel (ENUM REGISTER|GANTI_CHIP) tapi dulu
            // tidak pernah diisi eksplisit, sehingga register via web form menyisakan
            // NULL. Default-kan di sini supaya web form dan API konsisten.
            $model->detail_status_register ??= RegisterEnum::REGISTER;
            $model->detail_created_by ??= auth()->id();
        });

        static::updating(function (self $model) {
            $model->detail_updated_by = auth()->id();
        });
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontLogIfAttributesChangedOnly(['detail_updated_at', 'detail_updated_by'])
            ->useLogName('linen')
            ->setDescriptionForEvent(function (string $eventName) {
                return match ($eventName) {
                    'created' => "Detail linen {$this->detail_rfid} berhasil diregister.",
                    'updated' => "Detail linen {$this->detail_rfid} berhasil diperbarui.",
                    'deleted' => "Detail linen {$this->detail_rfid} berhasil dihapus.",
                    default => "Detail linen {$this->detail_rfid} mengalami perubahan pada {$eventName}.",
                };
            });
    }

    public function hasRs()
    {
        return $this->hasOne(Rs::class, 'rs_id', 'detail_id_rs');
    }

    public function hasRuangan()
    {
        return $this->hasOne(Ruangan::class, 'ruangan_id', 'detail_id_ruangan');
    }

    public function hasJenis()
    {
        return $this->hasOne(JenisLinen::class, 'jenis_id', 'detail_id_jenis');
    }

    public function hasBahan()
    {
        return $this->hasOne(JenisBahan::class, 'bahan_id', 'detail_id_bahan');
    }

    public function hasSupplier()
    {
        return $this->hasOne(Supplier::class, 'supplier_id', 'detail_id_supplier');
    }
}
