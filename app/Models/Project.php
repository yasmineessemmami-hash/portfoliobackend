<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $fillable = [
        'title',
        'description',
        'image',
        'image_type',
        'key',
        'tech_stack',
        'github_url',
        'live_url',
        'contact_email',
        'is_featured',
        'sort_order'
    ];

    protected $casts = [
        'tech_stack' => 'array',
        'is_featured' => 'boolean',
        'sort_order' => 'integer'
    ];
}
