<?php

namespace App\Http\Controllers;

use App\Booking;
use App\Services\HotelsService;
use BotMan\BotMan\BotMan;
use BotMan\Drivers\Facebook\Extensions\ButtonTemplate;
use BotMan\Drivers\Facebook\Extensions\Element;
use BotMan\Drivers\Facebook\Extensions\ElementButton;
use BotMan\Drivers\Facebook\Extensions\ListTemplate;
use BotMan\Drivers\Facebook\Extensions\GenericTemplate;
use Illuminate\Http\Request;

class HotelsController extends Controller
{
    protected $hotels_service;

    public function handle()
    {
        $botman = app('botman');
    }

    public function __construct(HotelsService $hotels_service)
    {
        $this->hotels_service = $hotels_service;
    }

    public function search(Request $request)
    {
        return $this->hotels_service->search($request->all());
    }

    public function getByCoords(Request $request)
    {
        return $this->hotels_service->getByCoords($request->all());
    }

    public function botman(BotMan $bot)
    {
        $message = $bot->getMessage()->getExtras();

        $hotels = $this->hotels_service->searchFromBotman($message['apiParameters']);
        $hotels = json_decode($hotels);

        $list = ListTemplate::create()
            ->useCompactView()
            ->addGlobalButton(ElementButton::create('view more')
                ->url('http://test.at')
            );

        foreach ($hotels as $hotel) {
            $list->addElement(Element::create($hotel->property_name ?? 'N/A')
                ->subtitle($hotel->property_name ?? 'N/A')
                ->image('https://picsum.photos/200/?random')
                ->addButton(ElementButton::create('visit')
                    ->url('https://helloromania.eu/hotel')
                )
            );
        }

        $bot->reply($list);

        //$bot->reply((string) count($hotels['results']));
    }

    public function debug(BotMan $bot, $location, $check_in, $check_out)
    {
        try {
            $hotels = $this->hotels_service->searchFromDebug(compact('location', 'check_in', 'check_out'));
            $hotels = json_decode($hotels, true);

            return $this->replyWithHotels($bot, $hotels);
        } catch (\Exception $e) {
            \Log::error($e->getMessage() . $e->getTraceAsString());
            $bot->reply('Ooops! :)');
            return $bot->reply($e->getMessage());
        }

        $bot->reply(substr(json_encode($hotels), 0, 200));
        return;
        $list = ListTemplate::create()
            ->useCompactView();

        foreach ($hotels['results'] as $hotel) {
            $list->addElement(Element::create($hotel['property_name'] ?? 'N/A')
                ->subtitle($hotel['property_name'] ?? 'N/A')
                ->image('https://picsum.photos/200/200/?image=' . rand(1, 1000))
                ->addButton(ElementButton::create('book now -'.$hotel['property_code'])
                    ->payload('book.hotel '.$hotel['property_code'])
                    ->type('postback')
                )
            );
        }

        $bot->reply($list);

        //$bot->reply((string) count($hotels['results']));
    }

    /*public function test(BotMan $bot)
    {
        $bot->reply(ButtonTemplate::create('Do you want to know more about BotMan?')
            ->addButton(ElementButton::create('Tell me more')
                ->type('postback')
                ->payload('tellmemore')
            )
            ->addButton(ElementButton::create('Show me the docs')
                ->url('http://botman.io/')
            )
        );
    }

    public function book(BotMan $bot, $property_code)
    {
        $bot->reply($property_code);
    }*/


    public function bookNow(BotMan $bot, $property_code)
    {
        Booking::create([
            'hotel_id' => $property_code,
            'data' => [],
            'status' => 'active'
        ]);

        $bot->reply('Booked: '. $property_code);
    }


    protected function showOneHotel(BotMan $bot, $hotel)
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

    protected function showHotelList(BotMan $bot, $hotels)
    {
        $count = count($hotels);
        if ($count < 2 || $count > 4) {
            return $bot->reply('Are you sure you want to see ' . $count . ' results?');
        }

        $list = ListTemplate::create()
            ->useCompactView();
        foreach ($hotels as $hotel) {
            $list->addElement(Element::create($hotel['property_name'] ?? 'N/A')
                ->subtitle($hotel['property_name'] ?? 'N/A')
                ->image('https://picsum.photos/200/200/?image=' . rand(1, 1000))
                ->addButton(ElementButton::create('book now -' . $hotel['property_code'])
                    ->payload('book.hotel ' . $hotel['property_code'])
                    ->type('postback')
                )
            );
        }
        $bot->reply($list);
    }

    protected function replyWithHotels(BotMan $bot, $hotels)
    {
        $num  = count($hotels['results']);
        \Log::info('num: ' . $num);
        if ($num == 0) {
            return $bot->reply('No hotels were found!');
        }
        if ($num == 1) {
            return $this->showOneHotel($bot, array_pop($hotels));
        }
        return $this->showHotelList($bot, array_slice($hotels, 0, 4));
    }


}
