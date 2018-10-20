<?php

use Faker\Generator as Faker;

function randomString($length = 5)
{
    $str = "";
    $characters = array_merge(range('A', 'Z'), range('a', 'z'), range('0', '9'));
    $max = count($characters) - 1;
    for ($i = 0; $i < $length; $i++) {
        $rand = mt_rand(0, $max);
        $str .= $characters[$rand];
    }
    return strtoupper($str);
}


$factory->define(App\Booking::class, function (Faker $faker) {

    return [
        'booking_id' => randomString(),
        'hotel_id'   => randomString(10),
        'data'       => json_encode(["column1" => $faker->word, "column2" => $faker->word, "column3" => $faker->word]),
        'status'     => $faker->randomElement(['active', 'cancelled'])
    ];
});
