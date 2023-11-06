<?php

namespace App\services;

use App\Models\CasinoOnline;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Intervention\Image\Facades\Image;
use Stevebauman\Location\Facades\Location;

class CasinoOnLineService
{

    public function getCasinoOnlineCollection( ):  Collection
    {

        return CasinoOnLine::where('actif', 1)->get();
    }

}
