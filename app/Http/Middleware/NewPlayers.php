<?php

namespace App\Http\Middleware;

use BotMan\BotMan\Interfaces\Middleware\Heard;
use BotMan\BotMan\Messages\Incoming\IncomingMessage;
use BotMan\BotMan\BotMan;
use App\Player;
use Log;

class NewPlayers implements Heard
{
    public function heard(IncomingMessage $message, $next, BotMan $bot)
    {
        try {
            Log::info(json_encode($bot->getUser()));
            return $next($message);

            $user = $bot->getUser();
            $user_id = $user->getId();
            $firstname = $user->getFirstName();
            $lastname  = $user->getLastName();

            $newUser = Player::where('facebook_id', $user_id)->get();

            if($newUser->isEmpty()) {
                $newUser = new Player(
                    [
                        'facebook_id'    => $user_id,
                        'firstname'      => $firstname,
                        'lastname'       => $lastname,
                        'avatar_url'     => "https://via.placeholder.com/50x50",
                        'rating'         => 60,
                        'bookings_total' => 0,
                    ]
                );
                $newUser->save();
            }
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }
        return $next($message);
    }
}
