<?php

namespace App\Services;

use GuzzleHttp\Client;

class HotelsService
{
    protected $key = 'yEjATjSWAbF2138WNIwU4kTxZruI80Qq';
    protected $url = 'https://api.sandbox.amadeus.com/v1.2/hotels/search-box';
    protected $client;
    protected $places_service;

    public function __construct()
    {
        $this->client = new Client;
        $this->places_service = new PlacesService;
    }

    public function search(array $query)
    {
        $place = $this->places_service->search($query['query']);

        $query['apikey'] = $this->key;
        $query['south_west_corner'] = "{$place['viewport']['southwest']['lat']},{$place['viewport']['southwest']['lng']}";
        $query['north_east_corner'] = "{$place['viewport']['northeast']['lat']},{$place['viewport']['northeast']['lng']}";

        $response = $this->client->get($this->url, [
            'query' => $query,
        ]);

        return $response->getBody()->getContents();
    }

    public function getByCoords(array $query)
    {
        $query['apikey'] = $this->key;

        $response = $this->client->get($this->url, [
            'query' => $query,
        ]);

        return $response->getBody()->getContents();
    }
}