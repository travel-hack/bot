<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use BotMan\BotMan\BotMan;
use App\Booking;
use function GuzzleHttp\json_decode;
use Illuminate\Support\Collection;

use BotMan\Drivers\Facebook\Extensions\ListTemplate;
use BotMan\Drivers\Facebook\Extensions\Element;
use BotMan\Drivers\Facebook\Extensions\ElementButton;

use App\Services\PlayerService;

class BookingController extends Controller
{
    public function handle()
    {
        $botman = app('botman');

        $botman->listen();
    }

    public function myBookings(BotMan $bot)
    {
        (new PlayerService())->check($bot);

        $bookings = Booking::where('status', 'active')->get();

        $list = ListTemplate::create()
            ->useCompactView();
        foreach ($bookings as $booking) {
            $list->addElement(Element::create('Awesome Booking')
                ->subtitle($booking->booking_id)
                ->image('https://www.clipartmax.com/png/middle/117-1179176_office-block-free-icon-office-building-flat-icon.png')
                ->addButton(ElementButton::create('cancel')
                    ->payload('book.cancel ' . $booking->booking_id)
                    ->type('postback')
                )
            );
        }
        $bot->reply('list');

        // $response = "Active Bookings: ";

        // foreach($bookings as $booking) {
        //      $response = $response . "\n" . $booking->booking_id;
        // }

        // $bot->reply($response);
    }

    public function allMyBookings(BotMan $bot)
    {
        $bookings = Booking::all();

        $this->replyWithTemplate($bot, $bookings);
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

    protected function replyWithTemplate(BotMan $bot, Collection $collection)
    {
        $bot->reply(ListTemplate::create()
            ->useCompactView()
            ->addGlobalButton(ElementButton::create('view more')
                ->url('http://test.at'))
            ->addElement(Element::create('BotMan Documentation')
                ->subtitle('All about BotMan')
                ->image('http://botman.io/img/botman-body.png')
                ->addButton(ElementButton::create('tell me more')
                    ->payload('tellmemore')
                    ->type('postback')))
            ->addElement(Element::create('BotMan Laravel Starter')
                ->subtitle('This is the best way to start with Laravel and BotMan')
                ->image('http://botman.io/img/botman-body.png')
                ->addButton(ElementButton::create('visit')
                    ->url('https://github.com/mpociot/botman-laravel-starter'))));
    }
}
