<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
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

// Автоматическая авторизация по токену
Route::get('/auto-login', [SignalsController::class, 'autoLogin'])->name('auto-login');

// Страница сигналов
Route::get('/signals', [SignalsController::class, 'show'])->name('signals');

// Временный маршрут для выполнения миграций (УДАЛИТЬ ПОСЛЕ ИСПОЛЬЗОВАНИЯ!)
Route::get('/run-migrations', function () {
    try {
        // Добавляем поле used в temp_tokens
        if (!Schema::hasColumn('temp_tokens', 'used')) {
            DB::statement('ALTER TABLE temp_tokens ADD COLUMN used TINYINT(1) NOT NULL DEFAULT 0 AFTER expires_at');
            DB::statement('ALTER TABLE temp_tokens ADD INDEX idx_temp_tokens_used (used)');
        }
        
        // Добавляем поля информации о пользователе в affiliate_links
        if (!Schema::hasColumn('affiliate_links', 'first_name')) {
            DB::statement('ALTER TABLE affiliate_links ADD COLUMN first_name VARCHAR(255) NULL AFTER telegram_id');
        }
        if (!Schema::hasColumn('affiliate_links', 'last_name')) {
            DB::statement('ALTER TABLE affiliate_links ADD COLUMN last_name VARCHAR(255) NULL AFTER first_name');
        }
        if (!Schema::hasColumn('affiliate_links', 'username')) {
            DB::statement('ALTER TABLE affiliate_links ADD COLUMN username VARCHAR(255) NULL AFTER last_name');
        }
        if (!Schema::hasColumn('affiliate_links', 'language_code')) {
            DB::statement('ALTER TABLE affiliate_links ADD COLUMN language_code VARCHAR(255) NULL AFTER username');
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Миграции выполнены успешно!',
            'tables_updated' => [
                'temp_tokens' => 'Добавлено поле used',
                'affiliate_links' => 'Добавлены поля first_name, last_name, username, language_code'
            ]
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage()
        ], 500);
    }
});
