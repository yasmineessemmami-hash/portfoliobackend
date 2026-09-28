<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SessionEvent extends Model
{
    protected $fillable = [
        'user_key',
        'app_key',
        'event',
    ];

    protected $casts = [
        'event' => 'array',
    ];

    /**
     * Get events for a session (user_key + app_key combination)
     */
    public static function getSessionEvents(string $userKey, string $appKey)
    {
        return self::where('user_key', $userKey)
            ->where('app_key', $appKey)
            ->orderBy('created_at')
            ->get()
            ->pluck('event')
            ->toArray();
    }
}
