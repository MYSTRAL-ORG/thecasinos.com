<?php

namespace App\Http\Controllers;

use App\Models\CasinoOnline;
use App\services\CasinoOnLineService;
use Illuminate\Http\Request;

class CasionOnLineController extends Controller
{
   //create a function get with request in parameters and retun a CasinoOnLine object
    public function get(Request $request, int $id,CasinoOnLineService $casinoOnLineService)
    {
       $casinoOnLine = CasinoOnLine::where('id', $id)->get()->first();
        $casinosOnLineActif = $casinoOnLineService->getCasinoOnlineCollection();
        $note = $casinoOnLine->note;
        $notePart1 = null;
        $notePart2 = null;
        if($note != null){
            $notePartArray =  explode(',',$casinoOnLine->note);
            $notePart1 = $notePartArray[0];
            if(count($notePartArray) > 1){
                $notePart2 = $notePartArray[1];
            }
        }



        return view('on-line/casino-online', compact('casinoOnLine' ,'casinosOnLineActif' ,'notePart1','notePart2'));
    }
}
