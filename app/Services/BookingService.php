<?php

namespace App\Services;
use App\Booking;
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
                Element::create('Booking')
                    ->subtitle("$booking->hotel_name -
                     Refund %: $contract->refund - 
                     Minimum Rating: $contract->minimum_rating -
                     Price: $booking->price - 
                     Booking code: $booking->id")
                    ->image($booking->hotel_image)
                    ->addButton(ElementButton::create('visit')
                        ->payload('book.visit ' . $booking->id)
                        ->type('postback'))
                    ->addButton(ElementButton::create('cancel')
                        ->payload('book.cancel ' . $booking->id)
                        ->type('postback')),
            ]);
    }
}
