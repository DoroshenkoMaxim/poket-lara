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

// Главная страница - сигналы
Route::get('/', [SignalsController::class, 'show'])->name('home');

// Telegram авторизация
Route::get('/login', [TelegramAuthController::class, 'showLoginForm'])->name('login');
Route::get('/telegram/auth', [TelegramAuthController::class, 'login'])->name('telegram.auth');
Route::post('/telegram/auth', [TelegramAuthController::class, 'login']);
Route::post('/telegram/register', [TelegramAuthController::class, 'register'])->name('telegram.register');

// Выход из системы
Route::post('/logout', function () {
    Auth::logout();
    return redirect('/');
})->name('logout');

// Telegram Bot маршруты
Route::post('/telegram/webhook', [TelegramBotController::class, 'webhook'])->name('telegram.webhook');
Route::get('/telegram/webhook-test', function() {
    return response()->json([
        'status' => 'webhook endpoint is accessible',
        'timestamp' => now(),
        'server' => request()->server(),
    ]);
})->name('telegram.webhook-test');
Route::get('/telegram/setup', [TelegramBotController::class, 'setWebhook'])->name('telegram.setup');
Route::get('/telegram/reinstall', [TelegramBotController::class, 'reinstallWebhook'])->name('telegram.reinstall');
Route::get('/telegram/clean-setup', [TelegramBotController::class, 'cleanAndSetupWebhook'])->name('telegram.clean-setup');
Route::get('/telegram/test-webhook', [TelegramBotController::class, 'testWebhook'])->name('telegram.test-webhook');
Route::get('/telegram/test-webhook-external', [TelegramBotController::class, 'checkWebhookExternal'])->name('telegram.test-webhook-external');
Route::get('/telegram/info', [TelegramBotController::class, 'getBotInfo'])->name('telegram.info');
Route::get('/telegram/diagnostics', function () {
    return view('telegram.diagnostics');
})->name('telegram.diagnostics');

// Обработка постбеков от PocketOption
Route::get('/postback', [PostbackController::class, 'handlePostback'])->name('postback');
Route::post('/postback', [PostbackController::class, 'handlePostback']);

// Страница сигналов
Route::get('/signals', [SignalsController::class, 'show'])->name('signals');

// Автоматическая авторизация
Route::get('/auto-login', [SignalsController::class, 'autoLogin'])->name('auto-login');
