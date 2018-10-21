<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(App\Player::class, 10)->create();
        factory(App\Booking::class, 10)->create();
        factory(App\Contract::class, 10)->create();
    }
}
