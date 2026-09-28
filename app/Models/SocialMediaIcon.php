<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SocialMediaIcon extends Model
{
    protected $fillable = [
        'key',
        'name',
        'icon',
        'library',
    ];
}
