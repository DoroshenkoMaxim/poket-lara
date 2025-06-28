<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\TelegramAuthController;
use App\Http\Controllers\TelegramBotController;
use App\Http\Controllers\PostbackController;
use App\Http\Controllers\SignalsController;

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

Route::get('/', function () {
    return view('welcome');
});

// Telegram авторизация
Route::get('/login', [TelegramAuthController::class, 'showLoginForm'])->name('login');
Route::post('/telegram/auth', [TelegramAuthController::class, 'login'])->name('telegram.auth');
Route::post('/telegram/register', [TelegramAuthController::class, 'register'])->name('telegram.register');

// Выход из системы
Route::post('/logout', function () {
    Auth::logout();
    return redirect('/');
})->name('logout');

// Telegram Bot маршруты
Route::post('/telegram/webhook', [TelegramBotController::class, 'webhook'])->name('telegram.webhook');
Route::get('/telegram/setup', [TelegramBotController::class, 'setWebhook'])->name('telegram.setup');
Route::get('/telegram/info', [TelegramBotController::class, 'getBotInfo'])->name('telegram.info');

// Обработка постбеков от PocketOption
Route::get('/postback', [PostbackController::class, 'handlePostback'])->name('postback');
Route::post('/postback', [PostbackController::class, 'handlePostback']);

// Страница сигналов
Route::get('/signals', [SignalsController::class, 'show'])->name('signals');

// Автоматическая авторизация
Route::get('/auto-login', [SignalsController::class, 'autoLogin'])->name('auto-login');

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home')->middleware('auth');
