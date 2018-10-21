<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use BotMan\BotMan\BotMan;
use App\Booking;
use function GuzzleHttp\json_decode;

use BotMan\Drivers\Facebook\Extensions\ListTemplate;
use BotMan\Drivers\Facebook\Extensions\GenericTemplate;
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
        (new PlayerService)->check($bot);

        $bookings = Booking::where('status', 'active')->get();

        $bot->reply(json_encode($bookings->all()));
        return;

        $list = ListTemplate::create()
            ->useCompactView();
        foreach ($bookings as $booking) {
            $list->addElement(Element::create('Awesome Booking')
                ->subtitle($booking->id)
                ->image('https://www.clipartmax.com/png/middle/117-1179176_office-block-free-icon-office-building-flat-icon.png')
                ->addButton(ElementButton::create('view')
                    ->payload('book.show ' . $booking->id)
                    ->type('postback')
                )
                // ->addButton(ElementButton::create('cancel')
                //     ->payload('book.cancel ' . $booking->id)
                //     ->type('postback')
                // )
            );
        }
        $bot->reply($list);

        // $response = "Active Bookings: ";

        // foreach($bookings as $booking) {
        //      $response = $response . "\n" . $booking->id;
        // }

        // $bot->reply($response);
    }

    public function allMyBookings(BotMan $bot)
    {
        $bookings = Booking::all();

        $this->replyWithTemplate($bot, $bookings);
    }

    public function showBooking(BotMan $bot, string $id)
    {
        $message = $bot->getMessage()->getExtras();
        
        $booking = Booking::find($message['apiParameters']['booking-id']);
        if (!$booking) {
            $bot->reply('Ha! Nice try! No such booking :)');
            return;
        }

        return $this->showOneBooking($bot, $booking);
    }

    public function cancelBooking(BotMan $bot)
    {
        $message = $bot->getMessage()->getExtras();
        $id = $message['apiParameters']['booking-id'];

        $success = Booking::where('id', $id)->update(['status' => 'cancelled']);

        if($success) {
            $bot->reply("Cancelled id " . $id);
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

    protected function showOneBooking(BotMan $bot, Booking $booking)
    {
        $template = GenericTemplate::create()
            ->addImageAspectRatio(GenericTemplate::RATIO_SQUARE)
            ->addElements([
                Element::create('BotMan Documentation')
                    ->subtitle('All about BotMan')
                    ->image('https://www.clipartmax.com/png/middle/117-1179176_office-block-free-icon-office-building-flat-icon.png')
                    ->addButton(ElementButton::create('visit')
                        ->url('http://botman.io'))
                    ->addButton(ElementButton::create('tell me more')
                        ->payload('tellmemore')
                        ->type('postback')),
                Element::create('BotMan Laravel Starter')
                    ->subtitle('This is the best way to start with Laravel and BotMan')
                    ->image('http://botman.io/img/botman-body.png')
                    ->addButton(ElementButton::create('visit')
                        ->url('https://github.com/mpociot/botman-laravel-starter')),
            ]);
        $bot->reply($template);
    }

    protected function showBookingList(BotMan $bot, $bookings)
    {
        $count = $bookings->count();
        if ($count < 2 || $count > 4) {
            return $bot->reply('Are you sure you want to see ' . $count . ' results?');
        }

        $list = ListTemplate::create()
            ->useCompactView();
        foreach ($bookings as $booking) {
            $list->addElement(Element::create('Awesome Booking')
                ->subtitle($booking->id)
                ->image('https://www.clipartmax.com/png/middle/117-1179176_office-block-free-icon-office-building-flat-icon.png')
                ->addButton(ElementButton::create('view')
                    ->payload('book.show ' . $booking->id)
                    ->type('postback'))
                // ->addButton(ElementButton::create('cancel')
                //     ->payload('book.cancel ' . $booking->id)
                //     ->type('postback')
                // )
            );
        }
        $bot->reply($list);
    }

    protected function replyWithBookings(BotMan $bot, $bookings)
    {
        if ($bookins->count() == 0) {
            return $bot->reply('No bookings were found!');
        }
        if ($bookings->count() == 1) {
            return $this->showOneBooking($bot, $bookings->first());
        }
        return $this->showBookingList($bot, $bookings);
    }
}
