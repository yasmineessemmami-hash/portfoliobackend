<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Skill extends Model
{
    protected $fillable = ['category_id', 'name', 'sort_order'];

    protected $casts = [
        'sort_order' => 'integer',
        'category_id' => 'integer'
    ];

    public function category()
    {
        return $this->belongsTo(SkillCategory::class, 'category_id');
    }
}
