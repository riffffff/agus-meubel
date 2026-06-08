<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Builder;

class Article extends Model
{
    protected $guarded = [];

    protected $casts = [
        'is_hero' => 'boolean',
        'published_at' => 'datetime',
    ];

    public function scopeHeroArticles(Builder $query): Builder
    {
        return $query->where('is_hero', true);
    }
}
