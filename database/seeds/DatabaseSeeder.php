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
        factory(App\Booking::class, 5)->create();
        factory(App\Contract::class, 5)->create();
    }
}
