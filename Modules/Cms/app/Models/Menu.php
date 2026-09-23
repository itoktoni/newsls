<?php

namespace Modules\Cms\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Cms\Concerns\SortOrderable;

#[Fillable(['name', 'slug', 'location', 'items', 'is_active', 'sort_order'])]
class Menu extends BaseModel
{
    use SoftDeletes, SortOrderable;

    protected $table = 'cms_menus';

    public static $sortColumns = ['name', 'slug', 'location', 'sort_order'];

    public static $filterColumns = ['name', 'slug', 'location'];

    public static function field_name(): string
    {
        return 'name';
    }

    protected function casts(): array
    {
        return [
            'items' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function getItemsCollection()
    {
        return collect($this->items ?? []);
    }

    public static function getByLocation(string $location)
    {
        return static::where('location', $location)
            ->where('is_active', true)
            ->first();
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255'],
            'location' => ['nullable', 'string', 'max:255'],
            'items' => ['nullable', 'array'],
            'is_active' => ['boolean'],
            'sort_order' => ['nullable', 'integer'],
        ];
    }
}
