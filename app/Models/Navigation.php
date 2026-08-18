<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Navigation extends Model
{
    protected $fillable = [
        'name',
        'slug',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(NavigationItem::class)
            ->whereNull('parent_id')
            ->orderBy('order');
    }

    protected function casts(): array
    {
        return [];
    }
}
