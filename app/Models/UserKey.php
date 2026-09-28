<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserKey extends Model
{
    protected $fillable = [
        'key',
        'label',
        'ip_address',
    ];

    /**
     * Generate a secure random key (4 random letters)
     */
    public static function generateKey(): string
    {
        $letters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $key = '';
        for ($i = 0; $i < 4; $i++) {
            $key .= $letters[random_int(0, strlen($letters) - 1)];
        }
        return $key;
    }

    /**
     * Find or create a user key
     * Accepts keys in format: 4 letters
     */
    public static function findOrCreate(string $key, ?string $ipAddress = null): self
    {
        // Validate key format - must be exactly 4 uppercase letters
        if (!preg_match('/^[A-Z]{4}$/', $key)) {
            // Invalid key, generate new one
            $key = self::generateKey();
        }

        return self::firstOrCreate(
            ['key' => $key],
            [
                'label' => 'unknown',
                'ip_address' => $ipAddress,
            ]
        );
    }
}
