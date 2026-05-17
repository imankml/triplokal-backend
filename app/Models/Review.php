<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'listing_id', 'user_id', 'source',
        'author_name', 'rating', 'comment', 'image_urls'
    ];

    protected $casts = [
        'image_urls' => 'array'
    ];

    public function listing()
    {
        return $this->belongsTo(Listing::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}