<?php

use App\Http\Controllers\CrawlerController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\MapkitController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\IndexContoller;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

//Route::get('/',  [MapkitController::class , 'fetchMapData']);
Route::get('/', [IndexContoller::class , 'index']);

Route::get('/leaflet', function () {
    return view('leaflet');
});

Route::get('/google', function () {
    return view('google');
});

Route::get('/mapKitData',  [CrawlerController::class , 'scrapeTableGames']);


Route::get('/mapKitData', [MapkitController::class , 'fetchMapData']);




