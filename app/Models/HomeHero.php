<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomeHero extends Model
{
    protected $fillable = [
        'status_text',
        'status_active',
        'full_name',
        'role_title',
        'headline',
        'subheadline',
    ];
}
