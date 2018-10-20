<?php
use App\Http\Controllers\BotManController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\HotelsController;
use BotMan\BotMan\Middleware\Dialogflow;

$botman = resolve('botman');

$botman->hears('Hi|Hello|Yo|Ola', function ($bot) {
    $bot->reply('Smells like a soon to be seasoned traveler :)');
});
$botman->hears('Start conversation', BotManController::class.'@startConversation');


/**
 * Bookings
 */
// list active bookings
$botman->hears('(show|list)?\s?(my)?\s(bookings|books|reservations|rezervations|resa)', BookingController::class . '@myBookings');
// list all bookings
$botman->hears('(show|list)?\s?all\s?(my)?\s(bookings|books|reservations|rezervations|resa)', BookingController::class . '@allMyBookings');
// show one booking
$botman->hears('(show|display)? (booking|book|reservation|rezervation|resa) {id}', BookingController::class . '@showBookings');
// cancel booking
$botman->hears('(cancel|delete)? (booking|book|reservation|rezervation|resa) {id}', BookingController::class . '@cancelBookings');


/**
 * Hotel search
 */
/*$dialogflow = Dialogflow::create('913d9d2423d74322a7af72d0ad47aafc')->listenForAction();
$botman->middleware->received($dialogflow);

$botman->hears('hotel.search', HotelsController::class . '@botman')->middleware($dialogflow);*/