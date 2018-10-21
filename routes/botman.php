<?php
use App\Http\Controllers\BotManController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\HotelsController;
use BotMan\BotMan\Middleware\Dialogflow;
use App\Http\Middleware\UserInputLogger;
use App\Http\Middleware\NewPlayers;

$botman = resolve('botman');


$dialogflow = Dialogflow::create('913d9d2423d74322a7af72d0ad47aafc')->listenForAction();
//$botman->middleware->received($dialogflow);


$userInputLogger = new UserInputLogger();
//$newPlayers = new NewPlayers();

$botman->middleware->received($userInputLogger);
//$botman->middleware->heard($newPlayers);

$botman->hears('Hi|Hello|Yo|Ola', function ($bot) {
    $bot->reply('Smells like a soon to be seasoned traveler :)');
});
// $botman->hears('Start conversation', BotManController::class.'@startConversation');

/**
 * Hotel search
 */
// $botman->hears('hotel.search', HotelsController::class . '@botman')->middleware($dialogflow);
$botman->hears('search hotels in {location} between {check_in} and {check_out}', HotelsController::class . '@debug');
/*$botman->hears('book.hotel {property_code}', HotelsController::class . '@book');
$botman->hears('test', HotelsController::class . '@test');*/
$botman->hears('book.now {property_code}', HotelsController::class . '@bookNow');


// $botman->hears('show.booking', BookingController::class . '@showBooking')->middleware($dialogflow);
// $botman->hears('cancel.booking', BookingController::class . '@cancelBooking')->middleware($dialogflow);


/**
 * Bookings
 */
// list active bookings
$botman->hears('(show|list)?\s?(me)?\s?(my)?\s(bookings|books|reservations|rezervations|resa)', BookingController::class . '@myBookings');
// list all bookings
$botman->hears('(show|list)?\s?(me)?\s?all\s?(my)?\s(bookings|books|reservations|rezervations|resa)', BookingController::class . '@allMyBookings');
// show one booking
$botman->hears('(show|display)? (booking|book|reservation|rezervation|resa) {id}', BookingController::class . '@showBooking');
$botman->hears('book.show {id}', BookingController::class . '@showBooking');
// cancel booking
$botman->hears('(cancel|delete)? (booking|book|reservation|rezervation|resa) {id}', BookingController::class . '@cancelBookings');
$botman->hears('book.cancel {id}', BookingController::class . '@cancelBooking');

$botman->hears('tripchat.user', function ($bot) {
    try {
        logger('user: ' . $bot->getSender());
    } catch (\Exception $e) {
        logger($e->getTraceAsString());
    }
    $bot->reply('Hello User!');
});
