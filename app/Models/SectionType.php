<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SectionType extends Model
{
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
