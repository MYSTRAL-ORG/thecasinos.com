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

        //Las Vegas
        $lon = -115.1352;
        $lat = 36.1450;
        try {
            $sessionGoogle = $googleService->createOrGetSessionApiMapTile();

       } catch (\Exception $e) {
            Log::info('Error', ['message' => $e->getMessage()]);
        } finally {
            return view('index2', compact('sessionGoogle', 'lon', 'lat',));
        }
    }

}



