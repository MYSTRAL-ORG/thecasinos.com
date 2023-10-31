<?php

namespace App\Http\Controllers;
use App\Models\Casino;
use App\services\GoogleService;
use App\services\LocationService;
use Carbon\Carbon;
use Goutte\Client;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
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
        $fromIndex= "true";
        try {
            $sessionGoogle = $googleService->createOrGetSessionApiMapTile();

       } catch (\Exception $e) {
            Log::info('Error', ['message' => $e->getMessage()]);
        } finally {
            return view('index', compact('sessionGoogle', 'lon', 'lat', 'fromIndex'));
        }
    }
    function categoryCity(Request $request, LocationService $locationService, GoogleService $googleService , String $city)
    {
        $casinos = DB::table('casino')
            ->join('casino_details', 'casino.id', '=', 'casino_details.id_casino')
            ->select('casino.name', 'casino.city_name','casino_details.resume_1_line', 'casino.img_url','casino.city_title','casino.country_title','casino.slug')
            ->where('casino.city_title', $city)->paginate(9);






        //Las Vegas
        $lon = -115.1352;
        $lat = 36.1450;
        $fromIndex= "true";
        try {
            $sessionGoogle = $googleService->createOrGetSessionApiMapTile();

        } catch (\Exception $e) {
            Log::info('Error', ['message' => $e->getMessage()]);
        } finally {
            return view('category', compact('sessionGoogle', 'lon', 'lat', 'fromIndex','casinos'));
        }
    }


    function categoryCountry(Request $request, LocationService $locationService, GoogleService $googleService , String $country)
    {
        $casinos = DB::table('casino')
            ->join('casino_details', 'casino.id', '=', 'casino_details.id_casino')
            ->select('casino.name', 'casino.city_name','casino_details.resume_1_line', 'casino.img_url','casino.city_title','casino.country_title','casino.slug')
            ->where('casino.country_title', $country)->paginate(9);

        //Las Vegas
        $lon = -115.1352;
        $lat = 36.1450;
        $fromIndex= "true";
        try {
            $sessionGoogle = $googleService->createOrGetSessionApiMapTile();

        } catch (\Exception $e) {
            Log::info('Error', ['message' => $e->getMessage()]);
        } finally {
            return view('category', compact('sessionGoogle', 'lon', 'lat', 'fromIndex','casinos'));
        }
    }

}



