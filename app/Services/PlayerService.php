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

        $newUser = Player::where('facebook_id', $user_id)->get();

        if($newUser->isEmpty()) {
            $newUser = new Player(
                [
                    'facebook_id'    => $user_id,
                    'firstname'      => $firstname,
                    'lastname'       => $lastname,
                    'avatar_url'     => $this->getUserPhoto($user),
                    'rating'         => 60,
                    'bookings_total' => 0,
                ]
            );
            $newUser->save();
        }
    }

    protected function getUserPhoto($user)
    {
        try {
            $info = $user->getInfo();
            if (array_key_exists('profile_pic', $info)) {
                return $info['profile_pic'];
            }
            return 'https://via.placeholder.com/50x50';
        } catch (\Exception $e) {
            return 'https://via.placeholder.com/50x50';
        }
    }
}
