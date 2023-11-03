<?php

use App\Http\Controllers\CasinoDetailsController;
use App\Http\Controllers\CasionOnLineController;
use App\Http\Controllers\CrawlerController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\MapkitController;
use App\Http\Controllers\OpenAiController;
use App\Models\CasinoDetail;
use App\Models\CasinoOnline;
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
Route::get('/', [IndexContoller::class , 'index'])->name('index');

Route::get('/online',  function () {
    return view('online');  // 'sample' corresponds to the sample.blade.php view file
})->name('online');


Route::get('/tttt', [OpenAiController::class , 'getListDataToCompute'])->name('getListDataToCompute');
Route::get('/ttttcat', [OpenAiController::class , 'getListCategoryToCompute'])->name('getListCategoryToCompute');
Route::get('/ttttcatCity', [OpenAiController::class , 'getListCategoryCityToCompute'])->name('getListCategoryCityToCompute');



Route::get('/casino-online/{id}', [CasionOnLineController::class , 'get'])->name('casino-online');


Route::resource('casinodetail', CasinoDetailsController::class);
Route::post('casinodetail/create', [CasinoDetailsController::class, 'create'])->name('casinodetails.create');
Route::get('casinodetail/{post}/show', [CasinoDetailsController::class, 'show'])->name('casinodetails.show');
Route::post('casinodetail/{id}/edit'  , [CasinoDetailsController::class, 'edit'])->name('casinodetails.edit');
Route::post('casinodetail/destroy', [CasinoDetailsController::class, 'destroy'])->name('casinodetails.destroy');
Route::post('casinodetail/delete', [CasinoDetailsController::class, 'delete'])->name('casinodetails.delete');
Route::post('casinodetail/{id}/update', [CasinoDetailsController::class, 'delete'])->name('casinodetails.update');

Route::get('/{country}/{city?}', [IndexContoller::class , 'category'])->name('category');

Route::get('/{country}/{city}/{name}', [LocationController::class , 'show'])->name('casino');

