<?php

namespace App\Http\Controllers;

use App\Booking;
use App\Contract;
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
                ->image('https://bot.tripchat.fun/images/hotel-' . rand(1, 50) . '.jpeg')
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
            $bot->typesAndWaits(1);
            $hotels = $this->hotels_service->searchFromDebug([
                'location' => $location, 
                'check_in' => '11-20',
                'check_out' => '11-22'
            ]);
            $hotels = json_decode($hotels, true);

            return $this->replyWithHotels($bot, $hotels);
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

            $booking = Booking::create($data);

            $contract = Contract::create([
                'booking_id' => $booking->id,
                'player_id' => $player->id,
                'minimum_rating' => 3,
                'refund' => ($booking->price / 100) * 10,
            ]);

            $bot->reply("Booking ID: $booking->id");
        } catch (\Exception $e) {
            \Log::error($e->getMessage() . $e->getTraceAsString());
            $bot->reply('Ooops! :)');
            return $bot->reply($e->getMessage());
        }
    }


    protected function showOneHotel(BotMan $bot, $hotel)
    {
        $data = $this->extractPropertyData($hotel);

        $template = GenericTemplate::create()
            ->addImageAspectRatio(GenericTemplate::RATIO_SQUARE)
            ->addElements([
                Element::create($hotel['property_name'] ?? 'Ramada Grand Resort')
                    ->subtitle($hotel['address']['line1'] ?? 'Downtown')
                    ->image($data['hotel_image'])
                    ->addButton(ElementButton::create("book at \${$hotel['total_price']['amount']}")
                        ->payload('book.now ' . json_encode($data))
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
            $data = $this->extractPropertyData($hotel);
            $list->addElement(Element::create($hotel['property_name'] ?? 'Ramada Grand Resort')
                ->subtitle($hotel['address']['line1'] ?? 'Downtown')
                ->image($data['hotel_image'])
                ->addButton(ElementButton::create("book at \${$hotel['total_price']['amount']}")
                    ->payload('book.now ' . json_encode($data))
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
            'hotel_image' => 'https://bot.tripchat.fun/images/hotel-' . rand(1, 50) . '.jpeg',
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
