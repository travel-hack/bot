<?php

namespace App\Http\Controllers;

use App\Services\HotelsService;
use BotMan\BotMan\BotMan;
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

        $bot->reply((string) count($hotels['results']));
    }

}
