<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AboutWorkProcess extends Model
{
    protected $fillable = ['step', 'title', 'description', 'sort_order'];

    protected $casts = [
        'sort_order' => 'integer'
    ];
}
