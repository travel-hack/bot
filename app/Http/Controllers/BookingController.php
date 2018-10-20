<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use BotMan\BotMan\BotMan;

class BookingController extends Controller
{
    public function handle()
    {
        $botman = app('botman');

        $botman->listen();
    }
    
    public function myBookings(BotMan $bot)
    {
        $bot->reply("Here are some bookings.");
    }

    public function showBookings(BotMan $bot, int $id)
    {
        $bot->reply("Booking with id " . $id);
    }

    public function cancelBookings(BotMan $bot, int $id)
    {
        $bot->reply("Canceling id " . $id);
    }
}
