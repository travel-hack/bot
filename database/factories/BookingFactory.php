<?php

use Faker\Generator as Faker;

$factory->define(App\Booking::class, function (Faker $faker) {

    return [
        'hotel_id'    => "",
        'hotel_name'  => "",
        'hotel_image' => "",
        'check_in'    => "",
        'check_out'   => "",
        'price'       => 0,
        'player_id'   => "",
        'status'      => "active"
    ];
});