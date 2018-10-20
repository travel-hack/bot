<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware([/*'auth:api'*/])->group(function () {

    Route::get('places/search', 'PlacesController@search');
    Route::get('hotels/get-by-coords', 'HotelsController@getByCoords');
    Route::get('hotels/search', 'HotelsController@search');

    Route::get('/players', "PlayerController@allPlayers");
    Route::get('/player/{id}', "PlayerController@getPlayer");
    Route::put('/player/{id}', "PlayerController@updatePlayer");

    Route::get('/contracts', "ContractController@allContracts");
    Route::get('/contact/{id}', "ContractController@getContract");
    Route::put('/contract/{id}', "ContractController@updateContract");
    Route::delete('/contract/{id}', "ContractController@cancelContract");
});
