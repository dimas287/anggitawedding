<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Chat extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id', 'sender_id', 'receiver_id', 'message',
        'attachment', 'is_read', 'is_internal',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'is_internal' => 'boolean',
    ];

    protected $appends = [
        'attachment_url',
        'attachment_type',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    public function getAttachmentUrlAttribute(): ?string
    {
        if (!$this->attachment) {
            return null;
        }

        return Storage::disk('public')->url($this->attachment);
    }

    public function getAttachmentTypeAttribute(): ?string
    {
        if (!$this->attachment) {
            return null;
        }

        try {
            $mime = Storage::disk('public')->mimeType($this->attachment);
        } catch (\Throwable $e) {
            $mime = null;
        }

        if (!$mime) {
            return null;
        }

        if (str_starts_with($mime, 'image/')) {
            return 'image';
        }

        if (str_starts_with($mime, 'video/')) {
            return 'video';
        }

        if (in_array($mime, ['application/pdf'])) {
            return 'document';
        }

        return 'file';
    }
}
