<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'category', 'phone', 'email', 'address',
        'description', 'base_price', 'instagram', 'photo', 'is_active',
    ];

    protected $casts = [
        'base_price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function bookingVendors()
    {
        return $this->hasMany(BookingVendor::class);
    }
}
