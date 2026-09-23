<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Menu extends BaseModel
{
    use SoftDeletes;

    protected $table = 'menus';

    protected $fillable = ['name', 'slug', 'location', 'items', 'is_active', 'sort_order'];

    protected $casts = [
        'items' => 'array',
        'is_active' => 'boolean',
    ];

    public static $sortColumns = ['name', 'slug', 'location', 'sort_order'];

    public static $filterColumns = ['name', 'slug', 'location'];

    public static function field_name(): string
    {
        return 'name';
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

    /**
     * Get menu items as a collection.
     */
    public function getItemsCollection()
    {
        return collect($this->items ?? []);
    }

    /**
     * Get active menu by location.
     */
    public static function getByLocation(string $location)
    {
        return static::where('location', $location)
            ->where('is_active', true)
            ->first();
    }
}
