<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactInfo extends Model
{
    protected $fillable = ['label', 'value', 'icon_key', 'type', 'sort_order'];

    protected $casts = [
        'sort_order' => 'integer'
    ];
}
