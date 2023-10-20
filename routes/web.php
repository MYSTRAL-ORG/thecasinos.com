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
Route::get('/', [IndexContoller::class , 'index'])->name('index2');



Route::get('/casino/{country}/{city}/{id}', [LocationController::class , 'show']) ->where(['id' => '[0-9]+'])->name('casino');




