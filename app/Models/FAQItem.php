<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FAQItem extends Model
{
    protected $table = 'faq_items';
    
    protected $fillable = ['question', 'answer', 'sort_order'];

    protected $casts = [
        'sort_order' => 'integer'
    ];
}
