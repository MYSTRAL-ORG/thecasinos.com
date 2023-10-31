<?php

namespace App\services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use GuzzleHttp\Client;
class GoogleService
{

    private Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function createOrGetSessionApiMapTile()
    {

        $sessionGoogle = Session::get('app.sessionGoogle');

        if (!$sessionGoogle) {

            $url = 'https://tile.googleapis.com/v1/createSession?key='.config('app.google_key');

            $data = [
                'language' => 'en-US',
                'region' => 'US',
                'mapType'=> 'satellite',
                'imageFormat'=>'jpeg',
                'scale'=> 'scaleFactor2x',
                'layerTypes'=> ['layerRoadmap']
            ];

            $headers = [
                'Content-Type' => 'application/json',
            ];

            $response = $this->client->post($url, [
                'json' => $data,
                'headers' => $headers,
            ]);

            $repJson = json_decode($response->getBody()->getContents());
        ;
            $sessionGoogle = $repJson->session;
            Session::put('app.sessionGoogle', $sessionGoogle);


        }
        return $sessionGoogle;

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
