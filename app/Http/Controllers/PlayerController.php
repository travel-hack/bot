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
            return Player::find($id);
        }
    }
}
