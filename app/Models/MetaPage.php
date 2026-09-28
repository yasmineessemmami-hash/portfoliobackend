<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MetaPage extends Model
{

    protected $fillable = [
        'page',
        'slug',
        'locale',
        'title',
        'description',
        'keywords',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'keywords' => 'array',
    ];
}
