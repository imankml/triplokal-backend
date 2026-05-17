<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Listing extends Model
{
    protected $table = 'listings';

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'category', 'name', 'address', 'description',
        'images', 'maps_url', 'price_per_night', 'price_per_ticket',
        'cuisine', 'amenities', 'is_active', 'owner_id', 'business_id',
        'latitude', 'longitude', 'capacity', 'opening_hours'
    ];

    protected $casts = [
        'images'    => 'array',
        'amenities' => 'array',
        'is_active' => 'boolean',
    ];

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function roomTypes()
    {
        return $this->hasMany(RoomType::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}