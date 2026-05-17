<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;

    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'id', 'email', 'password', 'name'
    ];

    protected $hidden = [
        'password'
    ];

    public function profile()
    {
        return $this->hasOne(Profile::class, 'id');
    }

    public function roles()
    {
        return $this->hasMany(UserRole::class);
    }

    public function listings()
    {
        return $this->hasMany(Listing::class, 'owner_id');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
}