<?php


use App\Http\Controllers\admin\AdminCasinoOnlineController;
use App\Http\Controllers\admin\CasinoController;
use App\Http\Controllers\admin\CasinoDetailController;
use App\Http\Controllers\admin\CategoryCityController;
use App\Http\Controllers\admin\CategoryController;
use App\Http\Controllers\admin\PasswordController;
use App\Http\Controllers\CasinoSearchController;
use App\Http\Controllers\CasinoOnlineController;
use App\Http\Controllers\CrawlerController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\MapkitController;
use App\Http\Controllers\OpenAiController;
use App\Models\CasinoDetail;
use App\Models\CasinoOnline;
use App\Models\CategoryCity;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\IndexController;

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
Route::get('/search-casinos', [CasinoSearchController::class, 'search'])->name('search_casinos');

Route::get(' 404', function () {
    return view('erros/404');
});


/***** ADMIN *****/
Route::get('/passwordprompt', function () {
    return view('passwordprompt');
});

Route::post('/password-check', [PasswordController::class, 'check'])->name('password.check');
Route::middleware(['password.protected'])->group(function () {
    Route::resource('/admin/casino', CasinoController::class);
    Route::resource('/admin/category', CategoryController::class);
    Route::resource('/admin/category-city', CategoryCityController::class);
    Route::resource('/admin/casino-online', AdminCasinoOnlineController::class);
    Route::resource('/admin/casino-details', CasinoDetailController::class);
    Route::resource('/admin', CategoryController::class);

});


/***** APP *****/


Route::get('/policy', function () {
    return view('policy');  // 'sample' corresponds to the sample.blade.php view file
})->name('policy');
Route::get('/terms', function () {
    return view('terms');  // 'sample' corresponds to the sample.blade.php view file
})->name('terms');
Route::get('/about', function () {
    return view('about');  // 'sample' corresponds to the sample.blade.php view file
})->name('about');


Route::get('/', [IndexController::class, 'index'])->name('index');
Route::get('/online', [IndexController::class, 'onLine'])->name('online');
Route::get('/online/{name}', [CasinoOnlineController::class, 'get'])->name('casino-online');

// Routes dynamiques avec expressions régulières pour 'country' et 'city'
Route::get('/{country}/{city}', [IndexController::class, 'category'])
    ->name('category')
    ->where([
        'country' => '[a-z-]+',
        'city' => '[a-z-]+'
    ]);

Route::get('/{country}/{city}/{name}', [LocationController::class, 'show'])
    ->name('casino')
    ->where([
        'country' => '[a-z-]+',
        'city' => '[a-z-]+',
        'name' => '[a-zA-Z0-9-_]+'
    ]);


Route::get('/tttt', [OpenAiController::class, 'getListDataToCompute'])->name('getListDataToCompute');
Route::get('/ttttcat', [OpenAiController::class, 'getListCategoryToCompute'])->name('getListCategoryToCompute');
Route::get('/ttttcatCity', [OpenAiController::class, 'getListCategoryCityToCompute'])->name('getListCategoryCityToCompute');

