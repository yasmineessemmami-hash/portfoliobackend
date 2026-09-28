<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ThemePalette extends Model
{
    protected $fillable = [
        'name',
        'palette',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'palette' => 'array',
        'is_active' => 'boolean',
    ];
}
