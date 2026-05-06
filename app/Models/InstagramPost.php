<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class InstagramPost extends Model
{
    use HasFactory;

    protected $fillable = [
        'instagram_url',
        'media_path',
        'media_url',
        'caption',
        'media_type',
        'sort_order',
        'is_active',
    ];

    public function getResolvedImageUrlAttribute()
    {
        if ($this->media_path) {
            return Storage::url($this->media_path);
        }
        
        return $this->media_url ?? 'https://images.unsplash.com/photo-1511795409834-ef04bbd61622?w=800';
    }
}
