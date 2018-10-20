<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Booking extends Model
{
    protected $table = 'bookings';
    protected $guarded = [];

    protected $casts = [
        'data' => 'array'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function (Model $model) {
            $model->booking_id = Str::random(16);
        });
    }
}
