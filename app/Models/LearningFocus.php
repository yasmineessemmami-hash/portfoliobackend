<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LearningFocus extends Model
{
    protected $table = 'learning_focus';
    protected $fillable = ['title', 'description', 'topics'];

    protected $casts = [
        'topics' => 'array'
    ];
}

