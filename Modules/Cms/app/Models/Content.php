<?php

namespace Modules\Cms\Models;

use App\Models\BaseModel;
use App\Models\User;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Fillable([
    'content_type_id',
    'title',
    'slug',
    'content',
    'excerpt',
    'status',
    'published_at',
    'author_id',
    'featured_image',
    'menu_order',
    'meta',
    'active_sections',
])]
class Content extends BaseModel
{
    protected $table = 'cms_contents';

    protected $attributes = [
        'status' => 'draft',
        'menu_order' => 0,
    ];

    public static $sortColumns = ['title', 'slug', 'status', 'published_at'];

    public static $filterColumns = ['title', 'slug', 'status'];

    public static function field_name(): string
    {
        return 'title';
    }

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
            'meta' => 'array',
            'active_sections' => 'array',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (self $model) {
            if ($model->status === 'published' && empty($model->published_at)) {
                $model->published_at = now();
            }
        });
    }

    public function has_type(): BelongsTo
    {
        return $this->belongsTo(Type::class, 'content_type_id');
    }

    public function has_author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function has_categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'cms_content_category', 'content_id', 'category_id');
    }

    public function has_tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'cms_content_tag', 'content_id', 'tag_id');
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'content' => ['nullable', 'string'],
            'excerpt' => ['nullable', 'string'],
            'status' => ['nullable', 'string', 'max:50'],
            'content_type_id' => ['nullable', 'integer'],
            'author_id' => ['nullable', 'integer'],
            'featured_image' => ['nullable', 'string', 'max:255'],
            'menu_order' => ['nullable', 'integer'],
            'meta' => ['nullable', 'array'],
            'active_sections' => ['nullable', 'array'],
            'active_sections.*' => ['integer'],
            'category_ids' => ['nullable', 'array'],
            'category_ids.*' => ['integer'],
            'tag_ids' => ['nullable', 'array'],
            'tag_ids.*' => ['integer'],
        ];
    }

    public function getMeta($fieldName)
    {
        $meta = $this->meta ?? [];

        return $meta[$fieldName] ?? null;
    }

    public function getAllMeta(): array
    {
        return $this->meta ?? [];
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    public function getIsPublishedAttribute(): bool
    {
        return $this->status === 'published' &&
               $this->published_at !== null &&
               $this->published_at <= now();
    }

    public function getNormalizedData(): array
    {
        $data = [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'content' => $this->content,
            'excerpt' => $this->excerpt,
            'status' => $this->status,
            'published_at' => $this->published_at?->toIso8601String(),
            'author_id' => $this->author_id,
            'featured_image' => $this->featured_image,
            'type' => $this->has_type?->slug,
        ];

        $meta = $this->meta ?? [];
        if (! empty($meta)) {
            $data['sections'] = $this->normalizeContainerMeta($meta);
        }

        return $data;
    }

    protected function normalizeContainerMeta(array $meta): array
    {
        $normalized = [];
        foreach ($meta as $key => $value) {
            if (str_starts_with($key, '_')) {
                continue;
            }
            if (is_array($value)) {
                $normalized[$key] = $this->addTypeToContainer($value);
            } else {
                $normalized[$key] = $value;
            }
        }

        return $normalized;
    }

    protected function addTypeToContainer(array $items): array
    {
        if (isset($items['_layout'])) {
            $items['_type'] = $items['_layout'];
            unset($items['_layout']);

            return $items;
        }
        foreach ($items as &$item) {
            if (is_array($item)) {
                if (isset($item['_layout'])) {
                    $item['_type'] = $item['_layout'];
                    unset($item['_layout']);
                }
                $item = $this->addTypeToContainer($item);
            }
        }

        return $items;
    }

    public function getBlueprintSchema(): array
    {
        $contentType = $this->has_type;
        if (! $contentType) {
            return [];
        }

        $sections = [];
        $fieldGroups = $contentType->has_sections()->where('is_active', true)->get();

        foreach ($fieldGroups as $group) {
            $sections[$group->name] = $group->getJsonSchema();
        }

        return [
            'content_type' => $contentType->slug,
            'type' => $contentType->type,
            'supports' => $contentType->supports,
            'sections' => $sections,
        ];
    }
}
