<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReplaySession extends Model
{
    protected $table = 'replay_sessions';
    
    protected $fillable = [
        'session_id',
        'user_key',
        'app_key',
        'created_at',
        'updated_at',
        'last_event_at',
        'event_count',
    ];

    public $timestamps = false; // We manage timestamps manually

    protected $casts = [
        'created_at' => 'datetime',
        'last_event_at' => 'datetime',
        'event_count' => 'integer',
    ];

    /**
     * Generate a unique session ID
     */
    public static function generateSessionId(): string
    {
        return bin2hex(random_bytes(32)); // 64 character hex string
    }

    /**
     * Get events for this session
     */
    public function events()
    {
        return $this->hasMany(ReplayEvent::class, 'session_id', 'session_id')
            ->orderBy('timestamp');
    }
}
