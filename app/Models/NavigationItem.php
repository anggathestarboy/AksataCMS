<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NavigationItem extends Model
{
    protected $fillable = [
        'navigation_id',
        'parent_id',
        'type',
        'url',
        'page_id',
        'open_in_new_tab',
        'order',
    ];

    protected static function booted(): void
    {
        static::created(fn (NavigationItem $item): bool => $item->navigation?->touch() ?? false);
        static::updated(fn (NavigationItem $item): bool => $item->navigation?->touch() ?? false);
        static::deleted(fn (NavigationItem $item): bool => $item->navigation?->touch() ?? false);
    }

    public function navigation(): BelongsTo
    {
        return $this->belongsTo(Navigation::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('order');
    }

    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }

    public function translations(): HasMany
    {
        return $this->hasMany(NavigationItemTranslation::class)->orderBy('id');
    }

    public function resolvedUrl(?string $locale = null): string
    {
        $locale ??= app()->getLocale();

        return match ($this->type) {
            'external', 'internal' => $this->url ?? '#',
            'page' => $this->pageUrl($locale),
        };
    }

    protected function pageUrl(string $locale): string
    {
        if ($this->page === null) {
            return '#';
        }

        $translation = $this->page->translations->firstWhere('locale', $locale);

        if ($translation === null) {
            return '#';
        }

        return '/'.$locale.'/'.$translation->slug;
    }

    public function label(?string $locale = null): string
    {
        $locale ??= app()->getLocale();

        return $this->translations->firstWhere('locale', $locale)?->label ?? '';
    }

    protected function casts(): array
    {
        return [
            'open_in_new_tab' => 'boolean',
        ];
    }
}
