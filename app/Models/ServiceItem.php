<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceItem extends Model
{
    protected $fillable = ['title', 'description', 'icon_key', 'color', 'features', 'sort_order'];

    protected $casts = [
        'features' => 'array',
        'sort_order' => 'integer'
    ];
}
