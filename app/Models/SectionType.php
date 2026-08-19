<?php

namespace App\Models;

use App\Services\SectionTemplateGenerator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SectionType extends Model
{
    protected static function booted(): void
    {
        static::created(function (SectionType $sectionType) {
            app(SectionTemplateGenerator::class)->generate($sectionType);
        });

        static::deleting(function (SectionType $sectionType) {
            app(SectionTemplateGenerator::class)->delete($sectionType);
        });
    }

    protected $fillable = [
        'name',
        'slug',
        'icon',
        'fields',
    ];

    public function sections(): HasMany
    {
        return $this->hasMany(Section::class);
    }

    protected function casts(): array
    {
        return [
            'fields' => 'array',
        ];
    }
}
