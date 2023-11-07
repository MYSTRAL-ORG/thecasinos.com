<?php


use App\Http\Controllers\admin\CategoryController;
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


Route::resource('/category',  CategoryController::class );






Route::get('/policy', function () {
    return view('policy');  // 'sample' corresponds to the sample.blade.php view file
})->name('policy');
Route::get('/terms', function () {
    return view('terms');  // 'sample' corresponds to the sample.blade.php view file
})->name('terms');
Route::get('/about', function () {
    return view('about');  // 'sample' corresponds to the sample.blade.php view file
})->name('about');


//Route::get('/',  [MapkitController::class , 'fetchMapData']);
Route::get('/', [IndexContoller::class , 'index'])->name('index');

Route::get('/online',   [IndexContoller::class , 'onLine'])->name('online');


Route::get('/tttt', [OpenAiController::class , 'getListDataToCompute'])->name('getListDataToCompute');
Route::get('/ttttcat', [OpenAiController::class , 'getListCategoryToCompute'])->name('getListCategoryToCompute');
Route::get('/ttttcatCity', [OpenAiController::class , 'getListCategoryCityToCompute'])->name('getListCategoryCityToCompute');

Route::get('/online/{name}', [CasionOnLineController::class , 'get'])->name('casino-online');

Route::get('/{country}/{city?}', [IndexContoller::class , 'category'])->name('category');

Route::get('/{country}/{city}/{name}', [LocationController::class , 'show'])->name('casino');





