<?php

use App\Services\PlayerService;

function check_user($bot)
{
    (new PlayerService())->check($bot);
}