<?php

namespace App\services;

use Intervention\Image\Facades\Image;
use GuzzleHttp\Client;
class GoogleService
{

    private Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function createSessionApiMapTile()
    {


        $url = 'https://tile.googleapis.com/v1/createSession?key='.config('app.google_key');
        // function to creation     session url

        $data = [
            'language' => 'en-US',
            'region' => 'US',
            'mapType'=> 'satellite',
            'imageFormat'=>'jpeg',
            'scale'=> 'scaleFactor2x',
            'layerTypes'=> ['layerRoadmap'],


        ];

        $headers = [
            'Content-Type' => 'application/json',
        ];

        $response = $this->client->post($url, [
            'json' => $data,
            'headers' => $headers,
        ]);

        return json_decode($response->getBody()->getContents());
    }

    function geoLocaliseIp(){
        $response = $this->client->post('https://www.googleapis.com/geolocation/v1/geolocate', [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'query' => ['key' => config('app.google_key')],
            'json' => [
                'considerIp' => true,
            ],
        ]);

        return json_decode($response->getBody()->getContents());
    }
}
