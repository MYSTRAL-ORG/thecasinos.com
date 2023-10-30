<?php

namespace App\Http\Controllers;

use App\Models\Casino;
use App\Models\CasinoDetail;
use App\services\GoogleService;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function show($country, $city, $slug,GoogleService $googleService)
    {


        //Las Vegas init by defauft
        $lon = -115.1352;
        $lat = 36.1450;


        $casino = Casino::where('slug',$slug)->first();

        $casinoDetail = CasinoDetail::where('id_casino',  $casino->id)->first();
        $lon =  (float) $casino->location_longitude;
        $lat = (float) $casino->location_latitude;
        $fromIndex= "false";

        $sessionGoogle = $googleService->createOrGetSessionApiMapTile();

        if (!$casinoDetail) {
            return abort(404);
        }

        return view('casino', compact('casinoDetail','casino','sessionGoogle','lon', 'lat', 'fromIndex' ));
    }
}
