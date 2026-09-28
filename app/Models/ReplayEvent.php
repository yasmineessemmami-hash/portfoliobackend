<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReplayEvent extends Model
{
    protected $table = 'replay_events';
    
    protected $fillable = [
        'session_id',
        'user_key',
        'app_key',
        'events',
        'timestamp',
    ];

    protected $casts = [
        'events' => 'array',
        'timestamp' => 'integer',
    ];
}
