<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'phone', 'address', 'avatar',
        'google_id',
        'last_online_at', 'last_ip_address',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_online_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isClient(): bool
    {
        return $this->role === 'client';
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function consultations()
    {
        return $this->hasMany(Consultation::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function invitations()
    {
        return $this->hasMany(Invitation::class);
    }

    public function chats()
    {
        return $this->hasMany(Chat::class, 'sender_id');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
}
