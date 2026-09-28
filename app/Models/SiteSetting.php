<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    protected $fillable = [
        'site_mode',
        'maintenance_until',
        'full_name',
        'contact_email',
        'contact_phone',
    ];

    protected $casts = [
        'maintenance_until' => 'datetime',
    ];
}
