<?php

namespace App\Http\Controllers;

use BotMan\BotMan\BotMan;
use Illuminate\Http\Request;
use App\Conversations\ExampleConversation;

class BotManController extends Controller
{
    /**
     * Place your BotMan logic here.
     */
    public function handle()
    {
        $botman = app('botman');

        $botman->listen();
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function tinker()
    {
        return view('tinker');
    }

    /**
     * Loaded through routes/botman.php
     * @param  BotMan $bot
     */
    public function startConversation(BotMan $bot)
    {
        $bot->startConversation(new ExampleConversation());
    }
    
    public function greetings(BotMan $bot)
    {
        try {
            
            check_user($bot);
            $user = $bot->getUser();
            $name = $user->getFirstName();
                        
            $bot->reply($this->getMessage($name));
            
        } catch (\Exception $e) {
            \Log::error($e->getMessage() . $e->getTraceAsString());
            $bot->reply('Ooops! :)');
            return $bot->reply($e->getMessage());
        }
    }
    
    protected function getMessage($name)
    {
        $answers = [
            "Hello $name! Seems to be cloudy today in Bucharest. How may I assist you? :)",
            "Good day $name! Hope this hackaton is going great! Is there anything I can do for you today? <3<3<3 ",
            "Greetings $name! Where would you like to travel? May I suggest Madrid? (Y)",
            "Hi $name! Do you know what the traveler and the tourist have in common? They both use TripChat! What’s your next stop?  8|",
            "Hi there $name! Happy to be your personal travel assistant! Where should we travel today? ;)" ,
        ];
        $key = rand(0, count($answers) - 1);
        return $answers[$key];
    }
}
