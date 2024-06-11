<?php

namespace App\Http\Controllers;

use App\Models\Casino;
use App\Models\Category;
use App\Models\CategoryCity;
use App\services\CasinoOnLineService;
use App\services\GoogleService;
use App\services\LocationService;
use Carbon\Carbon;
use Goutte\Client;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class IndexController extends Controller
{
    function index(Request $request, LocationService $locationService, GoogleService $googleService)
    {

        //Las Vegas
        $lon = -115.1352;
        $lat = 36.1450;
        $fromIndex = "true";
        try {
            $sessionGoogle = $googleService->createOrGetSessionApiMapTile();

        } catch (\Exception $e) {
            Log::info('Error', ['message' => $e->getMessage()]);
        } finally {
            return view('index', compact('sessionGoogle', 'lon', 'lat', 'fromIndex'));
        }
    }


    function category(Request $request, LocationService $locationService, GoogleService $googleService, string $country, string $city = null)
    {
        $fromIndex = 'false';

        $casinos = DB::table('casino')
            ->join('casino_details', 'casino.id', '=', 'casino_details.id_casino')
            ->select('casino.name', 'casino.city_name', 'casino_details.resume_1_line', 'casino.img_url', 'casino.city_title', 'casino.country_title', 'casino.slug', 'casino.country_name')
            ->where('casino.country_title', $country)->when($city, function ($query, $city) {
                return $query->where('casino.city_title', $city);
            })->paginate(9);

        $category = Category::where('country_title', $country)->get()->first();
        $categoryCity = null;
        if ($city != null) {
            $categoryCity = CategoryCity::where('city_title', $city)->get()->first();
        }
        Log::info('Request URL: ' . $request->fullUrl());
        $location = $casinos->items()[0]->country_name;
        if ($city != null) {
            $location = $casinos->items()[0]->city_name;
        }
        $casinoOnLineService = new CasinoOnLineService();
        $casinosOnLineActif = $casinoOnLineService->getCasinoOnlineCollection();
        return view('category', compact('casinos', 'location', 'category', 'categoryCity', 'casinosOnLineActif', 'fromIndex'));

    }

    function onLine(CasinoOnLineService $casinoOnLineService)
    {

        $casinosOnLineActif = $casinoOnLineService->getCasinoOnlineCollection();
        return view('online', compact('casinosOnLineActif'));

    }

}



