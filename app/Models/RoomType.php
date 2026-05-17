<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoomType extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'listing_id', 'name', 'description',
        'price_per_night', 'capacity', 'image_url',
        'sort_order', 'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function listing()
    {
        return $this->belongsTo(Listing::class);
    }
}