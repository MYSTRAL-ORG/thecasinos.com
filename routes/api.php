<?php

use App\Http\Controllers\OpenAiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
Route::post('/openai/{id}', [OpenAiController::class , 'create'])->name('openai');
Route::post('/openaiCat/{countryTitle}', [OpenAiController::class , 'insertHeader'])->name('openaiCat');
Route::post('/temp/openaiCatCity/{countryTitle}', [OpenAiController::class , 'insertHeaderCity'])->name('openaiCatCity');


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});




