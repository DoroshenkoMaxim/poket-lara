<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class TelegramAuthController extends Controller
{
    /**
     * Регистрация пользователя по telegram_id от бота
     */
    public function register(Request $request)
    {
        $request->validate([
            'telegram_id' => 'required|string|unique:users,telegram_id',
            'name' => 'required|string|max:255',
        ]);

        $user = User::create([
            'telegram_id' => $request->telegram_id,
            'name' => $request->name,
            'email' => 'telegram_' . $request->telegram_id . '@example.com',
            'password' => Hash::make(Str::random(32)), // случайный пароль
        ]);

        return response()->json([
            'success' => true,
            'message' => 'User registered successfully',
            'user_id' => $user->id
        ]);
    }

    /**
     * Авторизация пользователя через Telegram виджет
     */
    public function login(Request $request)
    {
        // Логируем входящие данные для отладки
        Log::info('Telegram auth attempt', [
            'all_data' => $request->all(),
            'query_params' => $request->query->all(),
        ]);

        $telegramData = $this->validateTelegramAuth($request->all());
        
        if (!$telegramData) {
            Log::warning('Telegram auth validation failed', [
                'data' => $request->all()
            ]);
            return redirect()->route('login')->with('error', 'Неверные данные авторизации Telegram. Проверьте настройки бота.');
        }

        Log::info('Telegram auth validated', ['telegram_data' => $telegramData]);

        $telegramId = $telegramData['id'] ?? null;
        
        if (!$telegramId) {
            return redirect()->route('login')->with('error', 'Не получен ID пользователя Telegram');
        }

        $user = User::where('telegram_id', $telegramId)->first();
        
        if (!$user) {
            // Автоматически создаем пользователя если его нет
            $firstName = $telegramData['first_name'] ?? 'Telegram User';
            $username = $telegramData['username'] ?? null;
            
            $name = $username ? $firstName . ' (@' . $username . ')' : $firstName;
            
            $user = User::create([
                'telegram_id' => $telegramId,
                'name' => $name,
                'email' => 'telegram_' . $telegramId . '@example.com',
                'password' => Hash::make(Str::random(32)),
            ]);
            
            Log::info('Created new user from Telegram auth', [
                'user_id' => $user->id,
                'telegram_id' => $telegramId,
                'name' => $name
            ]);
        }

        Auth::login($user);
        
        Log::info('User logged in via Telegram', [
            'user_id' => $user->id,
            'telegram_id' => $telegramId
        ]);
        
        return redirect()->intended('/')->with('success', 'Успешная авторизация через Telegram!');
    }

    /**
     * Валидация данных авторизации Telegram
     */
    private function validateTelegramAuth($data)
    {
        $bot_token = config('services.telegram.bot_token');
        
        if (!$bot_token) {
            Log::error('Bot token not configured');
            return false;
        }

        // Удаляем hash из данных
        $hash = $data['hash'] ?? '';
        if (!$hash) {
            Log::error('No hash provided in Telegram auth data');
            return false;
        }
        
        unset($data['hash']);

        // Сортируем данные и создаем строку для проверки
        ksort($data);
        $dataCheckString = '';
        
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                continue; // Пропускаем массивы
            }
            $dataCheckString .= $key . '=' . $value . "\n";
        }
        
        $dataCheckString = rtrim($dataCheckString, "\n");
        
        Log::info('Telegram auth validation', [
            'data_check_string' => $dataCheckString,
            'hash_provided' => $hash,
            'auth_date' => $data['auth_date'] ?? 'not_provided'
        ]);
        
        // Создаем секретный ключ
        $secretKey = hash('sha256', $bot_token, true);
        
        // Проверяем подпись
        $calculatedHash = hash_hmac('sha256', $dataCheckString, $secretKey);
        
        Log::info('Hash comparison', [
            'calculated' => $calculatedHash,
            'provided' => $hash,
            'match' => hash_equals($calculatedHash, $hash)
        ]);
        
        if (!hash_equals($calculatedHash, $hash)) {
            Log::warning('Telegram auth hash mismatch');
            return false;
        }

        // Проверяем время (данные должны быть не старше 1 дня)
        if (isset($data['auth_date']) && (time() - $data['auth_date']) > 86400) {
            Log::warning('Telegram auth data too old', [
                'auth_date' => $data['auth_date'],
                'current_time' => time(),
                'age_seconds' => time() - $data['auth_date']
            ]);
            return false;
        }

        Log::info('Telegram auth validation successful');
        return $data;
    }

    /**
     * Показать форму авторизации через Telegram
     */
    public function showLoginForm()
    {
        return view('auth.telegram-login');
    }
} 