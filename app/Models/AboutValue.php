<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AboutValue extends Model
{
    protected $fillable = ['key', 'title', 'description', 'sort_order'];
}
