<?php

namespace App\Http\Controllers;

use App\Services\HotelsService;
use BotMan\BotMan\BotMan;
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
        $hotels = json_decode($hotels, true);

        $list = ListTemplate::create()
            ->useCompactView()
            ->addGlobalButton(ElementButton::create('view more')
                ->url('http://test.at')
            );

        foreach ($hotels as $hotel) {
            $list->addElement(Element::create($hotel['property_name'])
                ->subtitle($hotel['property_name'])
                ->image('https://picsum.photos/200/?random')
                ->addButton(ElementButton::create('visit')
                    ->url('https://helloromania.eu/hotel')
                )
            );
        }

        $bot->reply($list);

        //$bot->reply((string) count($hotels['results']));
    }

}
