<?php

namespace App\Http\Controllers;

use App\Models\Casino;
use App\Models\CasinoDetail;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function show($country, $city, $id)
    {

        $casinoDetail = CasinoDetail::find($id);

        $casino = Casino::find($id);

        if (!$casinoDetail) {
            return abort(404);
        }

        return view('casino', compact('casinoDetail','casino'));
    }
}
