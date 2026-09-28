<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FAQHero extends Model
{
    protected $table = 'faq_heroes';
    
    protected $fillable = ['title', 'subtitle', 'description'];
}
