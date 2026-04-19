<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Consultation extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'consultation_code', 'user_id', 'booking_id', 'name', 'email',
        'phone', 'preferred_date', 'event_date', 'preferred_time',
        'consultation_type', 'message', 'meeting_notes', 'followup_notes',
        'status', 'reminder_sent',
    ];

    protected $casts = [
        'preferred_date' => 'date',
        'event_date' => 'date',
        'reminder_sent' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'pending' => 'Menunggu',
            'confirmed' => 'Terkonfirmasi',
            'done' => 'Selesai',
            'cancelled' => 'Dibatalkan',
            'converted' => 'Dikonversi ke Booking',
            default => $this->status,
        };
    }

    public static function generateCode(): string
    {
        $year = date('Y');
        $prefix = 'CONS' . $year;

        $lastCode = self::whereYear('created_at', $year)
            ->lockForUpdate()
            ->orderByDesc('consultation_code')
            ->value('consultation_code');

        $sequence = $lastCode ? ((int) substr($lastCode, -4)) + 1 : 1;

        return $prefix . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }
}
