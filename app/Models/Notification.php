<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends BaseModel
{
    protected $table = 'notifications';

    protected $keyType = 'int';

    protected $primaryKey = 'id';

    public $timestamps = true;

    public $incrementing = true;

    public static $filterColumns = [
        'id' => 'Id',
        'user_id' => 'User',
        'title' => 'Title',
        'type' => 'Type',
        'read' => 'Read',
    ];

    public static $sortColumns = [
        'id',
        'user_id',
        'title',
        'type',
        'read',
    ];

    protected $fillable = [
        'user_id',
        'icon',
        'icon_color',
        'title',
        'body',
        'url',
        'type',
        'read',
        'meta',
    ];

    protected function casts(): array
    {
        return [
            'read' => 'boolean',
            'meta' => 'array',
        ];
    }

    public function rules(): array
    {
        return [
            'user_id' => 'required',
            'title' => 'required|string|max:255',
        ];
    }

    public function toArray() {}

    public static function field_name()
    {
        return 'title';
    }

    public function has_user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
