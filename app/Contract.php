<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    protected $guarded = [];
    
    public function booking() 
    {
        return $this->belongsTo('App\Booking');
    }

    public function player()
    {
        return $this->belongsTo('App\Player');
    }
}
