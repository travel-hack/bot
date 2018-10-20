<?php

use Faker\Generator as Faker;


$factory->define(App\Player::class, function (Faker $faker) {

    return [
        'firstname'      => $faker->firstName,
        'lastname'       => $faker->lastName,
        'facebook_id'    => $faker->url,
        'avatar_url'     => "https://via.placeholder.com/50x50",
        'rating'         => $faker->numberBetween(0, 100),
        'bookings_total' => $faker->numberBetween(100, 1000),
    ];
});