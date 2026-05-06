<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CatalogLead extends Model
{
    protected $fillable = [
        'email',
        'ip_address',
        'catalog_version',
    ];
}
