<?php
use App\Http\Controllers\BotManController;
use App\Http\Controllers\BookingController;

$botman = resolve('botman');

$botman->hears('Hi', function ($bot) {
    $bot->reply('Hello!');
});
$botman->hears('Start conversation', BotManController::class.'@startConversation');


/**
 * Bookings
 */
// list bookings
$botman->hears('(show|list)?\s?(all)?\s?(my)?\s(bookings|books|reservations|rezervations|resa)', BookingController::class . '@myBookings');
// show one booking
$botman->hears('(show|display)? (booking|book|reservation|rezervation|resa) {id}', BookingController::class . '@showBookings');
// cancel booking
$botman->hears('(cancel|delete)? (booking|book|reservation|rezervation|resa) {id}', BookingController::class . '@cancelBookings');