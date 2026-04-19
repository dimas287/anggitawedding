<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id', 'user_id', 'payment_code', 'transaction_id', 'amount', 'type', 'method', 'status',
        'notes', 'proof_attachment', 'paid_at', 'confirmed_by', 'payment_response',
    ];

    protected $appends = ['proof_url'];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_response' => 'array',
        'paid_at' => 'datetime',
    ];

    public function getProofUrlAttribute(): ?string
    {
        if (!$this->proof_attachment) {
            return null;
        }

        return route('admin.secure.payment-proof', $this->id);
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function confirmedBy()
    {
        return $this->belongsTo(User::class, 'confirmed_by');
    }

    public static function generateCode(): string
    {
        $prefix = 'PAY';
        $timestamp = date('YmdHis');
        $random = strtoupper(substr(uniqid(), -4));
        return $prefix . $timestamp . $random;
    }
}
