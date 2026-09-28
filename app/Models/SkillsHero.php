<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SkillsHero extends Model
{
    protected $fillable = ['title', 'subtitle', 'description', 'cv_label', 'cv_file_url'];
}
