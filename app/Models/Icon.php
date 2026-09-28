<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Icon extends Model
{

    protected $fillable = [
        'key',
        'name',
        'icon',
        'library',
    ];
}
