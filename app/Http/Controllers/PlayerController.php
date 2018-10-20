<?php

namespace App\Http\Controllers;

use App\Player;
use Illuminate\Http\Request;

class PlayerController extends Controller
{

    public function allPlayers()
    {
        return Player::all();
    }
    
    public function getPlayer(int $id) 
    {
        return Player::find($id);
    }

    public function updatePlayer(int $id, Request $request)
    {
        $player = Player::find($id);
                
        $player->update($request->all());
        
        if($player->save()) {
            return $player;
        }
    }
}
