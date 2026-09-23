<?php

namespace Modules\Cms\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Cms\Concerns\SortOrderable;

#[Fillable([
    'name', 'label', 'type', 'config', 'rules', 'is_required',
    'default_value', 'sort_order', 'parent_id', 'mode', 'min', 'max',
    'collapsed', 'sortable', 'cloneable', 'layouts', 'type_id',
])]
class Field extends BaseModel
{
    use SortOrderable;

    protected $table = 'cms_fields';

    protected $attributes = [
        'sort_order' => 0,
        'is_required' => false,
        'mode' => 'multiple',
    ];

    public static $sortColumns = ['name', 'label', 'type', 'is_required', 'mode'];

    public static $filterColumns = ['name', 'label', 'type', 'mode'];

    public static function field_name(): string
    {
        return 'label';
    }

    protected function casts(): array
    {
        return [
            'config' => 'array',
            'rules' => 'array',
            'is_required' => 'boolean',
            'layouts' => 'array',
            'sort_order' => 'integer',
            'min' => 'integer',
            'max' => 'integer',
            'collapsed' => 'boolean',
            'sortable' => 'boolean',
            'cloneable' => 'boolean',
        ];
    }

    public function has_parent(): BelongsTo
    {
        return $this->belongsTo(Field::class, 'parent_id');
    }

    public function has_children(): HasMany
    {
        return $this->hasMany(Field::class, 'parent_id');
    }

    public static function getTypeOptions(): array
    {
        return [
            'text' => 'Text',
            'textarea' => 'Textarea',
            'wysiwyg' => 'WYSIWYG',
            'number' => 'Number',
            'email' => 'Email',
            'url' => 'URL',
            'date' => 'Date',
            'boolean' => 'Boolean',
            'select' => 'Select',
            'radio' => 'Radio',
            'checkbox' => 'Checkbox',
            'image' => 'Image',
            'gallery' => 'Gallery',
            'file' => 'File',
            'video' => 'Video',
            'color' => 'Color',
            'container' => 'Container',
        ];
    }

    public static function getContainerModes(): array
    {
        return ['single', 'multiple', 'flexible'];
    }

    public function isContainerType(): bool
    {
        return ! empty($this->mode) && in_array($this->mode, self::getContainerModes());
    }

    public function getLayouts(): array
    {
        return $this->layouts ?: [];
    }

    public function getJsonSchema(): array
    {
        $schema = [
            'name' => $this->name,
            'label' => $this->label,
            'type' => $this->type,
        ];

        if ($this->is_required) {
            $schema['required'] = true;
        }

        if (! empty($this->rules)) {
            $schema['rules'] = $this->rules;
        }

        if ($this->type === 'select' && ! empty($this->config['options'])) {
            $schema['options'] = $this->config['options'];
        }

        if ($this->mode === 'flexible') {
            $schema['mode'] = 'flexible';
            $schema['layouts'] = $this->getLayouts();
        }

        if (in_array($this->mode, ['multiple', 'flexible'])) {
            $schema['min'] = $this->min;
            $schema['max'] = $this->max;
        }

        if ($this->isContainerType() && $this->has_children->isNotEmpty()) {
            $schema['fields'] = $this->has_children->map(fn ($child) => $child->getJsonSchema())->toArray();
        }

        return $schema;
    }

    public function rules(): array
    {
        return [
            'label' => ['required', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'type' => ['nullable', 'string'],
            'default_value' => ['nullable', 'string'],
            'is_required' => ['nullable'],
            'sort_order' => ['nullable', 'integer'],
            'config_options' => ['nullable', 'string'],
            'children' => ['nullable', 'array'],
            'children.*.label' => ['nullable', 'string'],
            'children.*.name' => ['nullable', 'string'],
            'children.*.type' => ['nullable', 'string'],
            'children.*.is_required' => ['nullable'],
            'children.*.sort_order' => ['nullable', 'integer'],
            'children.*.id' => ['nullable', 'integer'],
            'children.*.children' => ['nullable', 'array'],
        ];
    }
}
