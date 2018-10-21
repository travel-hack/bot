<?php

use App\Services\PlayerService;

function check_user($bot)
{
    (new PlayerService())->check($bot);
}

function botman_log($bot, $message)
{
    return $bot->reply(substr(print_r($message, true), 0, 1500));
}