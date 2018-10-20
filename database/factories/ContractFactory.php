<?php

use Faker\Generator as Faker;

$factory->define(App\Contract::class, function (Faker $faker) {

    return [
        'booking_id'      => $faker->randomElement(['KJASD', 'JDLSU', 'KDHWGB']),
        'user_id'         => $faker->randomElement(['ASDJKSKSLADA2KDG3', 'ASDJKSALJDSA2KDG3', 'ALGJKSALJDSA2KDG3']),
        'minimum_rating'  => $faker->numberBetween(1,5),
        'expected_refund' => $faker->numberBetween(0,20),
    ];
});