<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AboutStat extends Model
{
    protected $fillable = ['key', 'value', 'label', 'sort_order'];
}
