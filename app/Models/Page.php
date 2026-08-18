<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Page extends Model
{
    protected $fillable = [
        'status',
        'published_at',
        'order',
    ];

    public function translations(): HasMany
    {
        return $this->hasMany(PageTranslation::class)->orderBy('id');
    }

    public function translation(?string $locale = null): ?PageTranslation
    {
        $locale ??= config('cms.default_locale');

        return $this->translations->firstWhere('locale', $locale)
            ?? $this->translations->firstWhere('locale', config('cms.default_locale'));
    }

    public function translationFor(string $locale): ?PageTranslation
    {
        return $this->translations->firstWhere('locale', $locale);
    }

    public function url(?string $locale = null): ?string
    {
        $translation = $this->translation($locale);

        if ($translation === null) {
            return null;
        }

        return '/' . $translation->locale . '/' . $translation->slug;
    }

    public function sections(): HasMany
    {
        return $this->hasMany(Section::class)->orderBy('order');
    }

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
        ];
    }
}
