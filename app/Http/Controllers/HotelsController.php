<?php

namespace App\Http\Controllers;

use App\Services\HotelsService;
use Illuminate\Http\Request;

class HotelsController extends Controller
{
    protected $hotels_service;

    public function __construct(HotelsService $hotels_service)
    {
        $this->hotels_service = $hotels_service;
    }

    public function search(Request $request)
    {
        return $this->hotels_service->search($request->all());
    }

    public function getByCoords(Request $request)
    {
        return $this->hotels_service->getByCoords($request->all());
    }

}
