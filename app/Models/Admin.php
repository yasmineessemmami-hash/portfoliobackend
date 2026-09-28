<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Admin extends Authenticatable implements JWTSubject
{
    protected $table = 'admins';
    protected $fillable = ['name', 'email', 'password'];

    protected $hidden = ['password', 'remember_token'];

    // JWT
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
}
