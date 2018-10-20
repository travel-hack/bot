<?php

namespace App\Services;

use SKAgarwal\GoogleApi\PlacesApi;

class PlacesService
{
    protected $key = 'AIzaSyCu1N9tNE8mEh_u4yI3SCOn3M_gV3CgL5E';
    protected $places;

    public function __construct()
    {
        $this->places = new PlacesApi($this->key);
    }

    public function search(string $query)
    {
        $place = $this->places->textSearch($query)->toArray();

        $result = $place['results'][0]['geometry']['location'] ?? null;

        return $result;
    }
}