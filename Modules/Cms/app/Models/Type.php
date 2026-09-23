<?php

namespace Modules\Cms\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

#[Fillable(['name', 'slug', 'type', 'description', 'supports', 'is_active', 'menu_position', 'menu_icon'])]
class Type extends BaseModel
{
    protected $table = 'cms_types';

    protected $attributes = [
        'type' => 'custom',
    ];

    public static $sortColumns = ['name', 'slug', 'type', 'is_active', 'menu_position'];

    public static $filterColumns = ['name', 'slug', 'type', 'is_active'];

    public static function field_name(): string
    {
        return 'name';
    }

    protected function casts(): array
    {
        return [
            'supports' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function has_contents(): HasMany
    {
        return $this->hasMany(Content::class, 'content_type_id');
    }

    public function has_sections(): HasMany
    {
        return $this->hasMany(Section::class, 'content_type_id');
    }

    public function has_field_groups(): HasMany
    {
        return $this->has_sections();
    }

    public static function getTypeOptions(): array
    {
        return [
            'page' => 'Page',
            'blog' => 'Blog',
            'product' => 'Product',
            'ecommerce' => 'Ecommerce',
            'custom' => 'Custom',
        ];
    }

    public static function getSupportsOptions(): array
    {
        return [
            'title' => 'Title',
            'slug' => 'Slug',
            'excerpt' => 'Excerpt',
            'featured_image' => 'Featured Image',
            'status' => 'Status',
            'author' => 'Author',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (self $model) {
            if (empty($model->slug) && ! empty($model->name)) {
                $model->slug = static::generateUniqueSlug($model->name, $model->id);
            }
        });

        static::updating(function (self $model) {
            if (empty($model->slug) && ! empty($model->name)) {
                $model->slug = static::generateUniqueSlug($model->name, $model->id);
            }
        });
    }

    public static function generateUniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $counter = 1;

        while (static::where('slug', $slug)
            ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
            ->exists()) {
            $slug = $originalSlug.'-'.$counter++;
        }

        return $slug;
    }

    public function hasContents(): bool
    {
        return $this->has_contents()->exists();
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'type' => ['required', 'string', 'in:page,blog,product,ecommerce,custom'],
            'description' => ['nullable', 'string'],
            'supports' => ['nullable', 'array'],
            'supports.*' => ['string', 'in:title,slug,excerpt,featured_image,status,author'],
            'is_active' => ['boolean'],
            'menu_position' => ['nullable', 'integer'],
            'menu_icon' => ['nullable', 'string', 'max:50'],
        ];
    }
}
