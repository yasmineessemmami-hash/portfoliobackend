<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SkillsSocialLink extends Model
{
    protected $fillable = ['platform', 'url', 'icon_key', 'sort_order'];
}

