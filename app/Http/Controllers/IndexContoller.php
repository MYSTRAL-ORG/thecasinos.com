<?php

namespace App\Http\Controllers;
use App\Models\SourceCasino;
use App\services\GoogleService;
use App\services\LocationService;
use Carbon\Carbon;
use Goutte\Client;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Storage;
use Stevebauman\Location\Facades\Location;
use Symfony\Component\DomCrawler\Crawler;

class IndexContoller extends Controller
{
    function index(Request $request, LocationService $locationService,GoogleService $googleService)
    {


        try {

            $sessionGoogle = Cache::get('app.sessionGoogle');

            if(!$sessionGoogle){

                $jsonGoogleSessionJson=  $googleService->createSession();
                $jsonGoogleSession =$jsonGoogleSessionJson['session'];
                Cache::put('app.sessionGoogle', $jsonGoogleSession , (new Carbon(intval($jsonGoogleSessionJson['expiry'])))->subDay());
                $sessionGoogle =$jsonGoogleSession;
            }





          /*  $lon =null;
            $lat = null;
            $ip= null;
            $externalIP = file_get_contents('https://geo.ipify.org/api/v2/country?apiKey=at_79U750U55UuikT4oBjHDsZbwv8ZSv&ipAddress=2a01:cb22:8d1:1800:78ff:811a:5ddd:535e'.Request::ip());

            $externalIPData = json_decode($externalIP);
            $location = Location::get($externalIPData->ip);

            $lon = $location->longitude;
            $lat = $location->latitude;
            $ip=   \Request::ip();
*/

       } catch (\Exception $e) {
           Log::info('Error', ['message' => $e->getMessage()]);
        }finally {
            return view('welcome',compact('sessionGoogle'));

        }
    }

}



