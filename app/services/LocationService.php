<?php

namespace App\services;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Intervention\Image\Facades\Image;
use Stevebauman\Location\Facades\Location;

class LocationService
{

    public function getLocation(String  $ip): JsonResponse
    {

        // Get location information based on the IP address
        $location = Location::get($ip);

        // Check if location information was successfully retrieved
        if ($location) {
            $latitude = $location->latitude;
            $longitude = $location->longitude;
            Log::info($latitude);
            Log::info($longitude);
            // You can now use $latitude and $longitude for further processing.

            return response()->json(['latitude' => $latitude, 'longitude' => $longitude]);
        } else {
            return response()->json(['error' => 'Unable to determine location.'], 400);
        }
    }

}
