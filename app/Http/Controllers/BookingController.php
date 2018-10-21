<?php

namespace App\Http\Controllers;

use App\Contract;
use App\Services\BookingService;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Outgoing\Question;
use Illuminate\Http\Request;
use BotMan\BotMan\BotMan;
use App\Booking;
use function GuzzleHttp\json_decode;

use BotMan\Drivers\Facebook\Extensions\ListTemplate;
use BotMan\Drivers\Facebook\Extensions\GenericTemplate;
use BotMan\Drivers\Facebook\Extensions\Element;
use BotMan\Drivers\Facebook\Extensions\ElementButton;
use App\Services\PlayerService;
use function GuzzleHttp\json_encode;
use App\Player;


class BookingController extends Controller
{
    public function handle()
    {
        $botman = app('botman');

        $botman->listen();
    }

    public function myBookings(BotMan $bot)
    {
        check_user($bot);

        $bookings = Booking::where('status', 'active')->get();

        return botman_log($bot, json_encode($bookings->all()));

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
        try {
            check_user($bot);
            $user = $bot->getUser();
            $user_id = $user->getId();
            $player = Player::whereFacebookId($user_id)->first();

            if (!$player) {
                return $bot->reply('This is akward Mr/Mrs ' . $user_id . '.I dont know who you are.');
            }
            
            $bookings = Booking::where([
                'player_id' => $player->id,
                'status' => 'active',
            ])
                ->limit(4)
                ->latest()
                ->get();

            $this->replyWithBookings($bot, $bookings);
        } catch (\Exception $e) {
            \Log::error($e->getMessage() . $e->getTraceAsString());
            $bot->reply('Ooops! :)');
            return $bot->reply($e->getMessage());
        }
       
    }

    public function showBooking(BotMan $bot, string $id)
    {
        try {
            $booking = Booking::find($id);
            if (!$booking) {
                $bot->reply('Ha! Nice try! No such booking :)');
                return;
            }

            return $this->showOneBooking($bot, $booking);
        } catch (\Exception $e) {
            \Log::error($e->getMessage() . $e->getTraceAsString());
            $bot->reply('Ooops! :)');
            return $bot->reply($e->getMessage());
        }
    }

    public function cancelBooking(BotMan $bot, string $id)
    {
        /*$message = $bot->getMessage()->getExtras();
        $id = $message['apiParameters']['booking-id'];*/

        try {
            $booking = Booking::where('id', $id);
            $b = Booking::where('id', $id)->update(['status' => 'cancelled']);
            $c = Contract::where('booking_id', $id)->update(['status' => 'closed']);

            if($b && $c) {
                $bot->reply("I went ahead and canceled your booking $id with hotel $booking->hotel_name");
            }
        } catch (\Exception $e) {
            \Log::error($e->getMessage() . $e->getTraceAsString());
            $bot->reply('Ooops! :)');
            return $bot->reply($e->getMessage());
        }
    }

    protected function showOneBooking(BotMan $bot, Booking $booking)
    {
        $template = (new BookingService)->showBooking($booking);
        $bot->reply($template);
    }

    protected function showBookingList(BotMan $bot, $bookings)
    {
        /*$count = $bookings->count();
        if ($count < 2 || $count > 4) {
            return $bot->reply('Are you sure you want to see ' . $count . ' results?');
        }*/

        $list = ListTemplate::create()
            ->useCompactView();
        foreach ($bookings as $booking) {
            $list->addElement(Element::create('Booking')
                ->subtitle("Booking $booking->id")
                ->image($booking->hotel_image)
                ->addButton(ElementButton::create('view')
                    ->payload('book.show ' . $booking->id)
                    ->type('postback'))
                // ->addButton(ElementButton::create('cancel')
                //     ->payload('book.cancel ' . $booking->id)
                //     ->type('postback'))
            );
        }
        $bot->reply($list);
    }

    protected function replyWithBookings(BotMan $bot, $bookings)
    {
        if ($bookings->count() == 0) {
            return $bot->reply('No bookings were found!');
        }
        if ($bookings->count() == 1) {
            return $this->showOneBooking($bot, $bookings->first());
        }
        return $this->showBookingList($bot, $bookings);
    }

    public function visit(BotMan $bot, $id)
    {
        try {
            $question = Question::create('Did you like your stay?')
                ->addButtons([
                    Button::create('Of course')->value('yes'),
                    Button::create('Hell no!')->value('no'),
                ]);

            $booking_service = new BookingService;

            $bot->ask($question, function (Answer $answer) use ($bot, $id, $booking_service) {
                try {
                    if ($answer->isInteractiveMessageReply()) {
                        $value = $answer->getValue(); // will be either 'yes' or 'no'
                        $text = $answer->getText(); // will be either 'Of course' or 'Hell no!'

                        if ($value === 'yes') {
                            $booking_service->review(5, $id);
                            $bot->reply('Thank you! We are happy that you enjoyed your stay!');
                        } else {
                            $booking_service->review(1, $id);
                            $bot->reply('Thank you! We are sad that you did not enjoy your stay!');
                            $bot->reply('Your refund has been processed.');
                        }
                    }
                } catch (\Exception $e) {
                    \Log::error($e->getMessage() . $e->getTraceAsString());
                    $bot->reply('Ooops! :)');
                    return $bot->reply($e->getMessage());
                }
            });
        } catch (\Exception $e) {
            \Log::error($e->getMessage() . $e->getTraceAsString());
            $bot->reply('Ooops! :)');
            return $bot->reply($e->getMessage());
        }
    }
}
