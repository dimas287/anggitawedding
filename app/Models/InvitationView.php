<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvitationView extends Model
{
    use HasFactory;

    protected $fillable = [
        'invitation_id',
        'ip_address',
        'user_agent',
    ];

    public function invitation()
    {
        return $this->belongsTo(Invitation::class);
    }
}
