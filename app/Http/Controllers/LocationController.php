<?php

namespace App\Http\Controllers;

use App\Models\Casino;
use App\Models\CasinoDetail;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function show($country, $city, $slug)
    {
        $casino = Casino::where('slug',$slug)->first();

        $casinoDetail = CasinoDetail::find($casino->id)->first();



        if (!$casinoDetail) {
            return abort(404);
        }

        return view('casino', compact('casinoDetail','casino'));
    }
}
