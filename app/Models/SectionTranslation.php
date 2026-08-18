<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SectionTranslation extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'section_id',
        'locale',
        'content',
    ];

    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class);
    }

    protected function casts(): array
    {
        return [
            'content' => 'array',
        ];
    }
}
