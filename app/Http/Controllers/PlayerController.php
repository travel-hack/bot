<?php

namespace App\Http\Controllers;

use App\Player;
use Illuminate\Http\Request;

class PlayerController extends Controller
{
    /**
     * Display the specified resource.
     *
     * @param  \App\Player  $player
     * @return \Illuminate\Http\Response
     */
    public function allPlayers()
    {
        $players = Player::all();
        return response()->json($players);
    }
    
    public function getPlayer(int $id) 
    {
        
    }

    public function updatePlayer(int $id, array $data)
    {

    }
}
