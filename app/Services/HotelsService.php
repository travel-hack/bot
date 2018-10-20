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

        $amenities = '';
        if (isset($query['amenities'])) {
            foreach(explode(',', $query['amenities']) as $term) {
                $term = $this->matchAmenity($term) ?? $term;
                $amenities .= "&amenity=$term";
            }
            unset($query['amenities']);
        }

        $query = http_build_query($query);

        $full_url = $this->url .'?'. $query . $amenities;

        $response = $this->client->get($full_url);

        return $response->getBody()->getContents();
    }

    public function searchFromBotman(array $message)
    {
        [$check_in, $check_out] = explode('/', $message['period']);

        $query = [
            'query' => $message['location']->city ?? null,
            'check_in' => $check_in,
            'check_out' => $check_out,
        ];

        return $this->search($query);
    }

    public function getByCoords(array $query)
    {
        $query['apikey'] = $this->key;

        $response = $this->client->get($this->url, [
            'query' => $query,
        ]);

        return $response->getBody()->getContents();
    }

    public function matchAmenity(string $term)
    {
        $amenities = [
            'accessible' => 'ACCESSIBLE_FACILITIES',
            'ballroom' => 'BALLROOM',
            'car-rental' => 'CAR_RENTAL',
            'casino' => 'CASINO',
            'conference' => 'CONFERENCE_FACILITIES',
            'doctor' => 'DOCTOR_ON_CALL',
            'elevator' => 'ELEVATORS',
            'free-internet' => 'FREE_HIGH_SPEED_INTERNET',
            'gym' => 'GYM',
            'wi-fi' => 'HOTSPOTS',
            'internet' => 'INTERNET_SERVICES',
            'jacuzzi' => 'JACUZZI',
            'bar' => 'LOUNGE_BARS',
            'massage' => 'MASSAGE_SERVICES',
            'non-smoking' => 'NON_SMOKING_ROOM',
            'parking' => 'PARKING',
            'pets' => 'PETS_ALLOWED',
            'pool' => 'POOL',
            'restaurant' => 'RESTAURANT',
            'room-service' => 'ROOM_SERVICE',
            'sauna' => 'SAUNA',
            'spa' => 'SPA',
        ];

        return $amenities[$term] ?? null;
    }
}