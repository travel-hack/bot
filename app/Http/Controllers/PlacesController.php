<?php

namespace App\Http\Controllers;

use App\Services\PlacesService;
use Illuminate\Http\Request;

class PlacesController extends Controller
{
    protected $places_service;

    public function __construct(PlacesService $places_service)
    {
        $this->places_service = $places_service;
    }

    public function search(Request $request)
    {
        return $this->places_service->search($request->get('query'));
    }

}
