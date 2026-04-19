<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class BookingVendor extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id', 'vendor_id', 'category', 'vendor_name',
        'contact', 'cost', 'status', 'notes', 'proof_attachment',
    ];

    protected $casts = [
        'cost' => 'decimal:2',
    ];

    protected $appends = ['proof_url'];

    public function getProofUrlAttribute(): ?string
    {
        if (!$this->proof_attachment) {
            return null;
        }

        return route('admin.secure.vendor-proof', $this->id);
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }
}
