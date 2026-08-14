<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Section extends Model
{
    protected $fillable = [
        'page_id',
        'section_type_id',
        'order',
    ];

    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }

    public function sectionType(): BelongsTo
    {
        return $this->belongsTo(SectionType::class);
    }

    public function translations(): HasMany
    {
        return $this->hasMany(SectionTranslation::class)->orderBy('id');
    }

    protected function casts(): array
    {
        return [];
    }
}
