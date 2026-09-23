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

    public static $sortColumns = [
        'id',
        'description',
        'event',
        'subject_type',
        'subject_id',
        'causer_type',
        'causer_id',
        'created_at',
    ];

    public static $filterColumns = [
        'log_name' => 'Log Name',
        'event' => 'Event',
        'description' => 'Description',
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

    public function getCauserNameAttribute(): ?string
    {
        return $this->causer?->name ?? null;
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
