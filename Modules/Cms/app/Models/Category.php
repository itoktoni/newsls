<?php

namespace Modules\Cms\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Cms\Concerns\SortOrderable;

#[Fillable(['name', 'slug', 'description', 'parent_id', 'sort_order'])]
class Category extends BaseModel
{
    use SoftDeletes, SortOrderable;

    protected $table = 'cms_categories';

    public static $sortColumns = ['name', 'slug', 'sort_order'];

    public static $filterColumns = ['name', 'slug'];

    public static function field_name(): string
    {
        return 'name';
    }

    public function has_parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function has_children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function has_contents(): BelongsToMany
    {
        return $this->belongsToMany(Content::class, 'cms_content_category', 'category_id', 'content_id');
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:cms_categories,slug,'.($this->id ?? '')],
            'description' => ['nullable', 'string'],
            'parent_id' => ['nullable', 'exists:cms_categories,id'],
            'sort_order' => ['nullable', 'integer'],
        ];
    }
}
