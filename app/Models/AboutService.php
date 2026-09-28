<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AboutService extends Model
{
    protected $fillable = ['title', 'description', 'features', 'sort_order'];

    protected $casts = [
        'features' => 'array',
        'sort_order' => 'integer'
    ];
}
