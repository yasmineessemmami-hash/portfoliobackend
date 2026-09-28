<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AboutIntroduction extends Model
{
    protected $fillable = [
        'avatar_image',
        'availability_active',
        'availability_text',
        'full_name',
        'role_title',
        'paragraphs',
        'tech_stack'
    ];

    protected $casts = [
        'paragraphs' => 'array',
        'tech_stack' => 'array',
        'availability_active' => 'boolean'
    ];
}
