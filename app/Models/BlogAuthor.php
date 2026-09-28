<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlogAuthor extends Model
{
    protected $fillable = ['name', 'avatar', 'bio', 'social_links'];

    protected $casts = [
        'social_links' => 'array'
    ];
}
