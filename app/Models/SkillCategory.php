<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SkillCategory extends Model
{
    protected $fillable = ['key', 'icon_key', 'title', 'description', 'sort_order'];

    protected $casts = [
        'sort_order' => 'integer'
    ];

    public function skills()
    {
        return $this->hasMany(Skill::class, 'category_id')->orderBy('sort_order');
    }
}
