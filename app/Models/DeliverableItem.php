<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliverableItem extends Model
{
    protected $fillable = ['title', 'description', 'icon_key', 'sort_order'];

    protected $casts = [
        'sort_order' => 'integer'
    ];
}
