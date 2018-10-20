<?php

namespace App\Http\Middleware;

use BotMan\BotMan\Interfaces\Middleware\Received;
use BotMan\BotMan\Messages\Incoming\IncomingMessage;
use BotMan\BotMan\BotMan;
use Raven_Client;

class UserInputLogger implements Received
{
    
    public function received(IncomingMessage $message, $next, BotMan $bot)
    {
        //$message->addExtras('custom_message_information', 'my custom value');

        $sentryClient = new Raven_Client($this->envSentryUrl());

        $sentryClient->captureMessage('User Interaction: ' . $message->getSender() . ' - ' . $message->getText());

        logger('User Interaction: ' . $message->getSender() . ' - ' . $message->getText());
        
        return $next($message);
    }
    
    protected function envSentryUrl() 
    {
        if($url = env('SENTRY_LARAVEL_DSN')) {
            return $url;
        }
        return "";    
    }
}
