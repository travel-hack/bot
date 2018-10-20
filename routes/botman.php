<?php
use App\Http\Controllers\BotManController;
use App\Http\Controllers\BookingController;

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