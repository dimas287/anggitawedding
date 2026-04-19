<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HeroSlide extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'subtitle',
        'media_type',
        'media_url',
        'media_path',
        'cta_label',
        'cta_url',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    protected $appends = ['resolved_media_url'];

    public function getResolvedMediaUrlAttribute(): string
    {
        if ($this->media_path) {
            return asset('storage/' . ltrim($this->media_path, '/'));
        }

        return $this->media_url;
    }
}
