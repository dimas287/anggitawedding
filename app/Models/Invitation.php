<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invitation extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id', 'user_id', 'template_id', 'slug',
        'groom_name', 'groom_short_name', 'bride_name', 'bride_short_name',
        'groom_father', 'groom_mother', 'groom_photo', 'groom_instagram',
        'bride_father', 'bride_mother', 'bride_photo', 'bride_instagram',
        'akad_datetime', 'akad_venue', 'akad_address',
        'reception_datetime', 'reception_venue', 'reception_address',
        'maps_link', 'love_story', 'love_story_items', 'opening_quote', 'closing_message',
        'hashtag', 'music_file', 'photo_prewedding', 'gallery_photos', 'media_files',
        'bank_accounts', 'qris_image',
        'is_published', 'rsvp_enabled', 'view_count',
    ];

    protected $casts = [
        'akad_datetime'      => 'datetime',
        'reception_datetime' => 'datetime',
        'gallery_photos'     => 'array',
        'media_files'        => 'array',
        'love_story_items'   => 'array',
        'bank_accounts'      => 'array',
        'is_published'       => 'boolean',
        'rsvp_enabled'       => 'boolean',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function template()
    {
        return $this->belongsTo(InvitationTemplate::class, 'template_id');
    }

    public function rsvps()
    {
        return $this->hasMany(Rsvp::class);
    }

    public function guestbook()
    {
        return $this->hasMany(\App\Models\GuestbookEntry::class);
    }

    public function views()
    {
        return $this->hasMany(InvitationView::class);
    }

    public function getPublicUrlAttribute(): string
    {
        return route('invitation.show', $this->slug);
    }

    public function getRsvpStatsAttribute(): array
    {
        return [
            'total' => $this->rsvps()->count(),
            'hadir' => $this->rsvps()->where('attendance', 'hadir')->count(),
            'tidak_hadir' => $this->rsvps()->where('attendance', 'tidak_hadir')->count(),
            'mungkin' => $this->rsvps()->where('attendance', 'mungkin')->count(),
            'total_guests' => $this->rsvps()->where('attendance', 'hadir')->sum('guests_count'),
        ];
    }
}
