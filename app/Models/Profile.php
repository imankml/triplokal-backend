<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id', 'display_name', 'avatar_url', 'phone', 'loyalty_points'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id');
    }
}