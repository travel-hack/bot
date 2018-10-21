<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\CustomTraits\CustomId;

class Booking extends Model
{
    use CustomId;
    
    protected $guarded = [];
    protected $primaryKey = 'booking_id';
    public $incrementing  = false;

    
    protected $casts = [
        'data' => 'array'
    ];

}
