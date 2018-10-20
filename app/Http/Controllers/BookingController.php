<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use BotMan\BotMan\BotMan;
use App\Booking;
use function GuzzleHttp\json_decode;

class BookingController extends Controller
{
    public function handle()
    {
        $botman = app('botman');

        $botman->listen();
    }
    
    public function myBookings(BotMan $bot)
    {
        $bookings = Booking::where('status', 'active')->get();

        $response = "Active Bookings: ";
        
        foreach($bookings as $booking) {
             $response = $response . "\n" . $booking->booking_id;
        }
        
        $bot->reply($response);
    }
    
    public function allMyBookings(BotMan $bot) 
    {
        $bookings = Booking::all();

        $response = "All Bookings: ";

        foreach ($bookings as $booking) {
            $response = $response . "\n" . $booking->booking_id;
        }
        $bot->reply($response);
    }

    public function showBookings(BotMan $bot, string $booking_id)
    {
        $booking = Booking::where('booking_id', $booking_id)->first();
                
        $bot->reply("Booking :" . $booking->data);
    }

    public function cancelBookings(BotMan $bot, string $booking_id)
    {
        $success = Booking::where('booking_id', $booking_id)->update(['status' => 'cancelled']);
        
        if($success) {
            $bot->reply("Cancelled id " . $booking_id);
        }
    }
}
