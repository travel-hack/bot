<?php

namespace App\Http\Controllers;

use App\Services\HotelsService;
use BotMan\BotMan\BotMan;
use BotMan\Drivers\Facebook\Extensions\ButtonTemplate;
use BotMan\Drivers\Facebook\Extensions\Element;
use BotMan\Drivers\Facebook\Extensions\ElementButton;
use BotMan\Drivers\Facebook\Extensions\ListTemplate;
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
        $hotels = $this->hotels_service->searchFromDebug(compact('location', 'check_in', 'check_out'));
        $hotels = json_decode($hotels, true);

        $list = ListTemplate::create()
            ->useCompactView();

        foreach ($hotels['results'] as $hotel) {
            $list->addElement(Element::create($hotel['property_name'] ?? 'N/A')
                ->subtitle($hotel['property_name'] ?? 'N/A')
                ->image('https://picsum.photos/200/?random')
                ->addButton(ElementButton::create('book now')
                    ->payload('book.hotel '.$hotel['property_code'])
                    ->type('postback')
                )
            );
        }

        $bot->reply($list);

        //$bot->reply((string) count($hotels['results']));
    }

    public function test(BotMan $bot)
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
    }

}
