<?php

namespace App\Services;
use App\Booking;
use App\Contract;
use BotMan\Drivers\Facebook\Extensions\Element;
use BotMan\Drivers\Facebook\Extensions\ElementButton;
use BotMan\Drivers\Facebook\Extensions\GenericTemplate;

class BookingService
{
    public function showBooking(Booking $booking)
    {
        $contract = $booking->contract;
        
        return GenericTemplate::create()
            ->addImageAspectRatio(GenericTemplate::RATIO_SQUARE)
            ->addElements([
                Element::create($booking->hotel_name)
                    ->subtitle("Booking code: $booking->id - Price: $$booking->price
                    Minimum Rating: $contract->minimum_rating - Refund: $$contract->refund")
                    ->image($booking->hotel_image)
                    ->addButton(ElementButton::create('visit')
                        ->payload('book.visit ' . $booking->id)
                        ->type('postback'))
                    ->addButton(ElementButton::create('cancel')
                        ->payload('book.cancel ' . $booking->id)
                        ->type('postback')),
            ]);
    }

    public function review($bot, $rating, $booking_id, $player)
    {
        $booking = Booking::find($booking_id);

        $player_rating = floor((100 - $player->rating) * min($booking->price / ($player->bookings_total / ($player->bookings_count + 1)), 2) / 3);

        if ($rating >= $booking->contract->minimum_rating) {
            $booking->update(['status' => 'closed']);
            $booking->contract->update(['status' => 'closed']);
            $bot->reply('Thank you! We are happy that you enjoyed your stay! :)');

        } else {
            $booking->update(['status' => 'closed']);
            $booking->contract->update(['status' => 'refunded']);
            $bot->reply('Thank you! We appreciate your honest feedback!');
            $bot->reply("Your refund (\${$booking->contract->refund}) has been processed.");

            $player_rating = -$player_rating;
        }

        $player->update(['rating' => $player_rating]);
    }
}
