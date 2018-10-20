<?php
use App\Http\Controllers\BotManController;
use App\Http\Controllers\BookingController;

$botman = resolve('botman');

$botman->hears('Hi', function ($bot) {
    $bot->reply('Hello!');
});
$botman->hears('Start conversation', BotManController::class.'@startConversation');


$botman->hears('my bookings', BookingController::class . '@myBookings');

$botman->hears('show booking {id}', BookingController::class . '@showBookings');

$botman->hears('cancel booking {id}', BookingController::class . '@cancelBookings');