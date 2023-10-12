<?php

namespace App\services;

use Intervention\Image\Facades\Image;
use GuzzleHttp\Client;
class GoogleService
{

    public function createSession()
    {
        $client = new Client();

        $url = 'https://tile.googleapis.com/v1/createSession?key='.config('app.google_key');
        // function to creation     session url

        $data = [
            'language' => 'en-US',
            'region' => 'US',
            'mapType'=> 'satellite',
            'imageFormat'=>'jpeg',
            'scale'=> 'scaleFactor1x',
            'layerTypes'=> ['layerRoadmap'],
            "styles" => [
                [
                    "stylers" => [
                        ["hue" => "#00ffe6"],
                        ["saturation" => -20],
                    ],
                ],
                [
                    "featureType" => "road",
                    "elementType" => "geometry",
                    "stylers" => [
                        ["lightness" => 100],
                        ["visibility" => "simplified"],
                    ],
                ],
            ],

        ];

        $headers = [
            'Content-Type' => 'application/json',
        ];

        $response = $client->post($url, [
            'json' => $data,
            'headers' => $headers,
        ]);

        $body = $response->getBody()->getContents();

        // Traitement de la réponse (par exemple, retourner la réponse ou effectuer d'autres opérations)
        return json_decode($body, true);
    }
}
