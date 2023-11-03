<?php

namespace App\Http\Controllers;

use App\Models\CasinoOnline;
use Illuminate\Http\Request;

class CasionOnLineController extends Controller
{
   //create a function get with request in parameters and retun a CasinoOnLine object
    public function get(Request $request, int $id)
    {
       $casinoOnLine = CasinoOnLine::where('id', $id)->get()->first();

        return view('on-line/casino-online', compact('casinoOnLine' ));
    }
}
