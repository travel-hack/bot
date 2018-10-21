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
            "Hello $name! Not the best weather we’re having today . How may I assist you ?",
            "Good day $name !Hope the hackaton is going great !Is there anything I can do for you today ?",
            "Greeting $name !Where would you like to travel ? The weather in Barcelona is great this month .",
            "Hi $name !What does the traveler and the tourist have in common ? They both use TripChat !What’s your next stop ?",
            "Hi there $name !I will be your personal travel assistant . Where should we travel to ?",
        ];
        $key = rand(0, count($answers) - 1);
        return $answers[$key];
    }
}
