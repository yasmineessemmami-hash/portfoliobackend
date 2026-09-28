<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactSocialLink extends Model
{
    protected $fillable = ['label', 'url', 'icon_key', 'sort_order'];

    protected $casts = [
        'sort_order' => 'integer'
    ];
}
