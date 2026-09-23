<?php

namespace Modules\Cms\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['name', 'slug'])]
class Tag extends BaseModel
{
    use SoftDeletes;

    protected $table = 'cms_tags';

    public static $sortColumns = ['name', 'slug'];

    public static $filterColumns = ['name', 'slug'];

    public static function field_name(): string
    {
        return 'name';
    }

    public function has_contents(): BelongsToMany
    {
        return $this->belongsToMany(Content::class, 'cms_content_tag', 'tag_id', 'content_id');
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:cms_tags,slug,'.($this->id ?? '')],
        ];
    }
}
