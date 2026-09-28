<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppKey extends Model
{
    protected $fillable = [
        'key',
        'source',
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
     * Get or create the default "unknown" app key
     */
    public static function getDefaultUnknownKey(): self
    {
        return self::firstOrCreate(
            ['key' => 'UNKN'],
            ['source' => 'unknown']
        );
    }

    /**
     * Find or create an app key
     * Accepts keys in format: 4 letters
     */
    public static function findOrCreate(string $key): self
    {
        // Validate key format - must be exactly 4 uppercase letters
        if (!preg_match('/^[A-Z]{4}$/', $key)) {
            // Invalid key, use default unknown key
            return self::getDefaultUnknownKey();
        }

        return self::firstOrCreate(
            ['key' => $key],
            ['source' => 'unknown']
        );
    }
}
