<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PackageMediaItem extends Model
{
    public const MAX_ITEMS_PER_PACKAGE = 20;

    protected $fillable = [
        'package_id',
        'media_type',
        'media_path',
        'video_url',
        'sort_order',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    protected $appends = ['url', 'embed_url'];

    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    public function getUrlAttribute(): ?string
    {
        if ($this->media_type === 'video' && $this->media_path) {
            return Storage::url($this->media_path);
        }

        if ($this->media_type === 'video' && $this->video_url) {
            return $this->video_url;
        }

        if ($this->media_path) {
            return Storage::url($this->media_path);
        }

        return null;
    }

    public function getEmbedUrlAttribute(): ?string
    {
        if ($this->media_type !== 'video' || empty($this->video_url)) {
            return null;
        }

        $url = $this->video_url;

        if (Str::contains($url, ['youtube.com', 'youtu.be'])) {
            $videoId = null;
            if (preg_match('~youtu\.be/(.+)$~', $url, $matches)) {
                $videoId = strtok($matches[1], '?');
            }
            if (preg_match('~v=([^&]+)~', $url, $matches)) {
                $videoId = $matches[1];
            }
            if ($videoId) {
                return 'https://www.youtube.com/embed/' . $videoId . '?rel=0&mute=1';
            }
        }

        if (Str::contains($url, 'vimeo.com')) {
            if (preg_match('~vimeo\.com/(\d+)~', $url, $matches)) {
                return 'https://player.vimeo.com/video/' . $matches[1];
            }
        }

        if (Str::contains($url, 'drive.google.com')) {
            return str_replace('/view', '/preview', $url);
        }

        return $url;
    }
}
