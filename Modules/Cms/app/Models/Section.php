<?php

namespace Modules\Cms\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Cms\Concerns\SortOrderable;

#[Fillable(['name', 'description', 'icon', 'content_type_id', 'field_ids', 'sort_order', 'is_active'])]
class Section extends BaseModel
{
    use SortOrderable;

    protected $table = 'cms_sections';

    public static $sortColumns = ['name', 'content_type_id', 'sort_order', 'is_active'];

    public static $filterColumns = ['name'];

    public static function field_name(): string
    {
        return 'name';
    }

    protected function casts(): array
    {
        return [
            'field_ids' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function has_type(): BelongsTo
    {
        return $this->belongsTo(Type::class, 'content_type_id');
    }

    public function getFieldsAttribute()
    {
        if (empty($this->field_ids)) {
            return collect();
        }

        $fields = Field::with('has_children')->whereIn('id', $this->field_ids)->get();
        $ids = array_flip($this->field_ids);

        return $fields->sortBy(fn ($f) => $ids[$f->id] ?? PHP_INT_MAX)->values();
    }

    public function getJsonSchema(): array
    {
        return [
            'name' => $this->name,
            'label' => $this->name,
            'description' => $this->description,
            'icon' => $this->icon,
            'fields' => $this->fields->map(fn ($field) => $field->getJsonSchema())->toArray(),
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (self $model) {
            $model->sort_order = $model->sort_order ?? 0;
        });
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'icon' => ['nullable', 'string', 'max:50'],
            'content_type_id' => ['required'],
            'field_ids' => ['nullable', 'array'],
            'sort_order' => ['nullable', 'integer'],
            'is_active' => ['boolean'],
        ];
    }
}
