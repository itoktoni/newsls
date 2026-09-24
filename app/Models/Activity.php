<?php

namespace App\Models;

use Abbasudo\Purity\Traits\Filterable;
use Abbasudo\Purity\Traits\Sortable;
use App\Concerns\DefaultEntity;
use App\Properties\ActivityEntity;
use Spatie\Activitylog\Models\Activity as SpatieActivity;

/**
 * @mixin SpatieActivity
 */
class Activity extends SpatieActivity
{
    use ActivityEntity, DefaultEntity, Filterable, Sortable;

    protected $table = 'activity_log';

    protected $primaryKey = 'id';

    public $incrementing = true;

    protected function casts(): array
    {
        return [
            'attribute_changes' => 'collection',
            'properties' => 'collection',
        ];
    }

    // ponytail: kolom tabel: log_name (nama operasi), subject_id (RFID),
    // subject_model_name (basename subject_type), causer_name (users.name hasil
    // join di ActivityLogController), created_at (Tanggal). `event` tampil
    // sebagai badge di kartu mobile, `description` tetap bisa difilter/di-export
    // — keduanya masih bisa disortir lewat ActivityLogController::SORTABLE.
    // `causer_id` tidak ditampilkan.
    // ponytail: log_name = tipe operasi (LogType::REGISTER/KOTOR/...) —
    // ditampilkan paling depan agar sekali lihat mencerminkan event-nya.
    public static $sortColumns = [
        'log_name',
        'event',
        'subject_id',
        'subject_model_name',
        'causer_name',
        'created_at',
    ];

    // ponytail: key = nama yang dikirim filter/quick-search, value = label.
    // `user_name` = users.name (butuh join), `subject_type` = kolom FQCN —
    // dipilih dari dropdown (label basename) supaya user tidak mengetik
    // `App\Models\...` manual.
    public static $filterColumns = [
        'log_name' => 'Nama',
        'event' => 'Event',
        'subject_id' => 'ID (RFID)',
        'subject_type' => 'Model',
        'user_name' => 'User',
        'description' => 'Deskripsi',
        'created_at' => 'Tanggal',
    ];

    public static function field_name(): string
    {
        return 'id';
    }

    public function rules(): array
    {
        return [];
    }

    public static function field_primary(): string
    {
        return 'id';
    }

    /**
     * ponytail: $value = kolom hasil join `users.name as causer_name` yang
     * dipilih ActivityLogController. Accessor menang atas kolom (Laravel
     * memanggil accessor dengan nilai mentah kolomnya), jadi nilai join
     * dipakai lebih dulu; relasi causer hanya fallback untuk query yang
     * tidak join (mis. Activity::with('causer')).
     */
    public function getCauserNameAttribute(?string $value = null): ?string
    {
        return $value ?? $this->causer?->name;
    }

    public function getCauserEmailAttribute(): ?string
    {
        return $this->causer?->email ?? null;
    }

    public function getSubjectNameAttribute(): ?string
    {
        return $this->subject?->name ?? null;
    }

    public function getSubjectModelNameAttribute(): string
    {
        if ($this->subject_type === null) {
            return '-';
        }

        return class_basename($this->subject_type);
    }
}
