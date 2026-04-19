<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class PortfolioImage extends Model
{
    protected $fillable = [
        'image_path',
        'title',
        'caption',
        'aspect',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'sort_order' => 'integer',
        'is_active' => 'boolean',
    ];

    public function mediaItems()
    {
        return $this->hasMany(PortfolioMediaItem::class)
            ->orderBy('sort_order')
            ->orderBy('id');
    }

    protected static function booted(): void
    {
        static::deleting(function (PortfolioImage $image) {
            $image->loadMissing('mediaItems');

            foreach ($image->mediaItems as $media) {
                if ($media->media_path) {
                    Storage::disk('public')->delete($media->media_path);
                }
            }

            if ($image->image_path) {
                Storage::disk('public')->delete($image->image_path);
            }
        });
    }
}
