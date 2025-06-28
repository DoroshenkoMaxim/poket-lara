<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CurrencyController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// API для валют (публичные маршруты)
Route::prefix('currencies')->group(function () {
    Route::get('/', [CurrencyController::class, 'apiIndex']);
    Route::get('/stats', [CurrencyController::class, 'stats']);
    Route::get('/best', [CurrencyController::class, 'getBest']);
    Route::get('/{symbol}', [CurrencyController::class, 'show']);
});

// API для валют (защищенные маршруты)
Route::middleware('auth:sanctum')->prefix('currencies')->group(function () {
    Route::post('/update-from-pocket-option', [CurrencyController::class, 'updateFromPocketOption']);
    Route::get('/parse-now', [CurrencyController::class, 'parseNow']);
    Route::put('/{symbol}', [CurrencyController::class, 'update']);
});
