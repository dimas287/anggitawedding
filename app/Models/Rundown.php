<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rundown extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id', 'time', 'activity', 'description',
        'pic', 'location', 'duration_minutes', 'sort_order', 'is_done',
    ];

    protected $casts = [
        'is_done' => 'boolean',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}
