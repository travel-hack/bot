<?php

namespace App\Services;
use BotMan\BotMan\BotMan;
use App\Player;

class PlayerService
{

    public function check(BotMan $bot)
    {
        try {
            $this->findOrCreate($bot);
        } catch (\Exception $e) {
            \Log::error('PlayerService error: ' . $e->getMessage());
        }
    }

    protected function findOrCreate(BotMan $bot)
    {
        $user = $bot->getUser();
        $user_id = $user->getId();
        $firstname = $user->getFirstName();
        $lastname  = $user->getLastName();

        \Log::info('user info' . print_r($user->getInfo(), true));

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
    }
}
