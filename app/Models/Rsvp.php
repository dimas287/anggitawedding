<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rsvp extends Model
{
    use HasFactory;

    protected $table = 'rsvps';

    protected $fillable = [
        'invitation_id', 'name', 'phone', 'guests_count',
        'attendance', 'message', 'ip_address',
    ];

    public function invitation()
    {
        return $this->belongsTo(Invitation::class);
    }
}
