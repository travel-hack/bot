<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Player extends Model
{    
    protected $guarded = [];
    
    public function bookings() 
    {
        return $this->hasMany('App\Booking');
    }
    
    public function contracts() 
    {
        return $this->hasMany('App\Contract');
    }
}
