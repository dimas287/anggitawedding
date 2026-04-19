<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GuestbookEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'invitation_id', 'name', 'message', 'ip_address',
    ];

    public function invitation()
    {
        return $this->belongsTo(Invitation::class);
    }
}
