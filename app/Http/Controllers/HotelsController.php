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
use App\Player;

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
    }


    public function custom(BotMan $bot, $location)
    {
        try {
            return botman_log($bot, $location);
        } catch (\Exception $e) {
            \Log::error($e->getMessage() . $e->getTraceAsString());
            $bot->reply('Ooops! :)');
            return $bot->reply($e->getMessage());
        }
    }
    
    public function bookNow(BotMan $bot, $data)
    {
        try {
            check_user($bot);
            $user = $bot->getUser();
            $user_id = $user->getId();

            \Log::info('User_id' . $user_id);

            $player = Player::whereFacebookId($user_id)->first();

            if (!$player) {
                return $bot->reply('Who are you? ' . $user_id);
            }

            \Log::info(json_encode($player));

            //return botman_log($bot, $property_code);

            $data = \GuzzleHttp\json_decode($data, true);
            $data['player_id'] = $player->id;

            Booking::create($data);

            $bot->reply('Booked: '. $data['hotel_id']);
        } catch (\Exception $e) {
            \Log::error($e->getMessage() . $e->getTraceAsString());
            $bot->reply('Ooops! :)');
            return $bot->reply($e->getMessage());
        }
    }


    protected function showOneHotel(BotMan $bot, $hotel)
    {
        $template = GenericTemplate::create()
            ->addImageAspectRatio(GenericTemplate::RATIO_SQUARE)
            ->addElements([
                Element::create($hotel['property_name'] ?? 'N/A')
                    ->subtitle($hotel['property_name'] ?? 'N/A')
                    ->image('https://picsum.photos/200/200/?image=' . rand(1, 1000))
                    ->addButton(ElementButton::create('book now')
                        ->payload('book.now ' . json_encode($this->extractPropertyData($hotel)))
                        ->type('postback')
                    )
            ]);
        $bot->reply($template);
    }

    protected function showHotelList(BotMan $bot, $hotels)
    {
        /*$count = count($hotels);
        if ($count < 2 || $count > 4) {
            return $bot->reply('Are you sure you want to see ' . $count . ' results?');
        }*/

        $list = ListTemplate::create()
            ->useCompactView();
        foreach ($hotels as $hotel) {
            $list->addElement(Element::create($hotel['property_name'] ?? 'N/A')
                ->subtitle($hotel['property_name'] ?? 'N/A')
                ->image('https://picsum.photos/200/200/?image=' . rand(1, 1000))
                ->addButton(ElementButton::create('book now')
                    ->payload('book.now ' . json_encode($this->extractPropertyData($hotel)))
                    ->type('postback')
                )
            );
        }
        $bot->reply($list);
    }

    protected function extractPropertyData($hotel)
    {
        return [
            'hotel_id' => $hotel['property_code'],
            'hotel_name' => $hotel['property_name'],
            'hotel_image' => 'https://picsum.photos/200/200/?image=' . rand(1, 1000),
            'check_in' => $hotel['rooms'][0]['rates'][0]['start_date'],
            'check_out' => $hotel['rooms'][0]['rates'][0]['end_date'],
            'price' => $hotel['total_price']['amount'],
        ];
    }

    protected function replyWithHotels(BotMan $bot, $hotels)
    {
        $num  = count($hotels['results']);
        \Log::info('num: ' . $num);
        if ($num == 0) {
            return $bot->reply('No hotels were found!');
        }
        if ($num == 1) {
            return $this->showOneHotel($bot, array_pop($hotels['results']));
        }
        return $this->showHotelList($bot, array_slice($hotels['results'], 0, 4));
    }


}
