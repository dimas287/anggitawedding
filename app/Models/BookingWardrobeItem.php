<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookingWardrobeItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'created_by',
        'item_name',
        'wearer',
        'category',
        'size',
        'color',
        'accessories',
        'notes',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
