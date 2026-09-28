<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SocialLink extends Model
{
    protected $fillable = [
        'owner_type',
        'owner_id',
        'platform',
        'url',
        'icon_key',
        'sort_order',
    ];
}
