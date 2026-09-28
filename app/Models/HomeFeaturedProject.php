<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomeFeaturedProject extends Model
{
    protected $fillable = [
        'title',
        'description',
        'image',
        'image_type',
        'icon_key',
        'tech',
        'sort_order',
    ];
}
