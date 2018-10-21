<?php

use Faker\Generator as Faker;

$factory->define(App\Booking::class, function (Faker $faker) {

    return [
        'hotel_id'    => "fromGeorgeApiFlowRege",
        'hotel_name'  => "George's Hotel",
        'hotel_image' => "http://georgesef.example/example.png",
        'check_in'    => $faker->date(),
        'check_out'   => $faker->date(),
        'price'       => 0,
        'player_id'   => App\Player::inRandomOrder()->get()->first()->id,
        'status'      => "active"
    ];
});