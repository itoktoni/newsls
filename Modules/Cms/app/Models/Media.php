<?php

namespace Modules\Cms\Models;

use App\Models\BaseModel;
use App\Models\User;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

#[Fillable([
    'filename',
    'original_filename',
    'mime_type',
    'size',
    'disk',
    'path',
    'thumbnail_path',
    'alt',
    'title',
    'caption',
    'width',
    'height',
    'user_id',
    'meta',
])]
class Media extends BaseModel
{
    use SoftDeletes;

    protected $table = 'cms_media';

    public static function field_name(): string
    {
        return 'filename';
    }

    protected function casts(): array
    {
        return [
            'meta' => 'array',
            'size' => 'integer',
            'width' => 'integer',
            'height' => 'integer',
        ];
    }

    public function has_user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getUrlAttribute(): string
    {
        return Storage::disk($this->disk)->url($this->path);
    }

    public function getThumbnailAttribute(): ?string
    {
        if ($this->thumbnail_path && Storage::disk($this->disk)->exists($this->thumbnail_path)) {
            return Storage::disk($this->disk)->url($this->thumbnail_path);
        }

        return null;
    }

    public function isImage(): bool
    {
        return str_starts_with($this->mime_type, 'image/');
    }

    public function getHumanSizeAttribute(): string
    {
        $bytes = $this->size;
        $units = ['B', 'KB', 'MB', 'GB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2).' '.$units[$i];
    }

    public function scopeOfType($query, string $type)
    {
        return $query->where('mime_type', 'like', $type.'%');
    }

    public function scopeImages($query)
    {
        return $query->where('mime_type', 'like', 'image/%');
    }
}
