<?php

namespace App\Http\Controllers;
use App\services\GoogleService;
use App\services\LocationService;
use Carbon\Carbon;
use Goutte\Client;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class IndexContoller extends Controller
{
    function index(Request $request, LocationService $locationService, GoogleService $googleService)
    {

        $lon = -115.1352;
        $lat = 36.1450;
   try {
            $sessionGoogle = Cache::get('app.sessionGoogle');

           /* if (!$sessionGoogle) {
                $jsonGoogleSessionJson = $googleService->createSessionApiMapTile();
                $jsonGoogleSession = $jsonGoogleSessionJson->session;

                $expiry = (new Carbon(intval($jsonGoogleSessionJson->expiry)))->subDay();
                Cache::put('app.sessionGoogle', $jsonGoogleSession, $expiry);
                $sessionGoogle = $jsonGoogleSession;
            }*/

            $location = $request->session()->get('location');

           /* if (!$location) {
                $locationJson = $googleService->geoLocaliseIp();
                $request->session()->put('location', $locationJson);
                $location = $locationJson;
            }

            $lon = $location->location->lng;
            $lat = $location->location->lat;*/

            $directory = public_path('/img/casinos/randomCasinos');
            $files = File::allFiles($directory);


            $fileNamesRadomCasinos = collect($files)->map(function ($file) {
                return $file->getFilename();
            })->implode('|');

       } catch (\Exception $e) {
            Log::info('Error', ['message' => $e->getMessage()]);
        } finally {
            return view('welcome', compact('sessionGoogle', 'lon', 'lat','fileNamesRadomCasinos'));
        }
    }

}



