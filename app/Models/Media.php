<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Media extends Model
{
    protected $fillable = [
        'name',
        'file_name',
        'mime_type',
        'size',
        'path',
        'alt_text',
        'created_by',
        'width',
        'height',
        'loading',
        'slug',
        'folder_id',
    ];

    protected static ?array $pathCache = null;

    public function getUrlAttribute(): string
    {
        return Storage::disk('public')->url($this->path);
    }

    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function folder(): BelongsTo
    {
        return $this->belongsTo(Folder::class);
    }

    public function scopeSearch(Builder $query, ?string $search): Builder
    {
        if (blank($search)) {
            return $query;
        }

        return $query->where('name', 'like', "%{$search}%")
            ->orWhere('file_name', 'like', "%{$search}%");
    }

    public function scopeInFolder(Builder $query, ?int $folderId): Builder
    {
        return $query->where('folder_id', $folderId);
    }

    public static function findByPath(string $path): ?self
    {
        if (static::$pathCache === null) {
            static::$pathCache = static::query()
                ->pluck('id', 'path')
                ->all();
        }

        $id = static::$pathCache[$path] ?? null;

        if ($id === null) {
            return static::where('path', $path)->first();
        }

        return static::find($id);
    }

    public static function clearPathCache(): void
    {
        static::$pathCache = null;
    }

    public static function readDimensionsFromUploadedFile(string $filePath): ?array
    {
        $fullPath = Storage::disk('public')->path($filePath);

        if (! file_exists($fullPath)) {
            return null;
        }

        $size = @getimagesize($fullPath);

        if ($size === false) {
            return null;
        }

        return [
            'width' => $size[0],
            'height' => $size[1],
        ];
    }

    public function readDimensionsFromExistingFile(): ?array
    {
        return static::readDimensionsFromUploadedFile($this->path);
    }

    public static function resolveMetadataFromPath(string $path): array
    {
        $media = static::findByPath($path);

        if (! $media) {
            return [];
        }

        return [
            'width' => $media->width,
            'height' => $media->height,
            'alt' => $media->alt_text ?? $media->name,
            'loading' => $media->loading ?? 'lazy',
        ];
    }
}
