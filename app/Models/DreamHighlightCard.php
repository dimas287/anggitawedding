<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DreamHighlightCard extends Model
{
    use HasFactory;

    protected $fillable = [
        'image_path',
        'image_url',
        'title',
        'subtitle',
        'quote',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    protected $appends = ['resolved_image_url'];

    public function getResolvedImageUrlAttribute(): ?string
    {
        if ($this->image_path) {
            return asset('storage/' . ltrim($this->image_path, '/'));
        }

        return $this->image_url;
    }
}
