<?php

use Faker\Generator as Faker;

$factory->define(App\Booking::class, function (Faker $faker) {

    return [
        'booking_id' => $faker->randomElement(['KJASD', 'JDLSU', 'KDHWGB']),
        'hotel_id'   => $faker->randomNumber(5),
        'data'       => json_encode(["pepeni" => "spanac"]),
        'status'     => $faker->randomElement(['active', 'canceled']),
    ];
});
