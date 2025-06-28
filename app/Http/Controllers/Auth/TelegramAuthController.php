<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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
            'email' => null, // email не обязательный для Telegram авторизации
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
        $telegramData = $this->validateTelegramAuth($request->all());
        
        if (!$telegramData) {
            return redirect()->route('login')->with('error', 'Неверные данные авторизации Telegram');
        }

        $user = User::where('telegram_id', $telegramData['id'])->first();
        
        if (!$user) {
            return redirect()->route('login')->with('error', 'Пользователь не найден. Обратитесь к боту для регистрации.');
        }

        Auth::login($user);
        
        return redirect()->intended('/home');
    }

    /**
     * Валидация данных авторизации Telegram
     */
    private function validateTelegramAuth($data)
    {
        $bot_token = config('services.telegram.bot_token');
        
        if (!$bot_token) {
            return false;
        }

        // Удаляем hash из данных
        $hash = $data['hash'] ?? '';
        unset($data['hash']);

        // Сортируем данные и создаем строку для проверки
        ksort($data);
        $dataCheckString = '';
        
        foreach ($data as $key => $value) {
            $dataCheckString .= $key . '=' . $value . "\n";
        }
        
        $dataCheckString = rtrim($dataCheckString, "\n");
        
        // Создаем секретный ключ
        $secretKey = hash('sha256', $bot_token, true);
        
        // Проверяем подпись
        $calculatedHash = hash_hmac('sha256', $dataCheckString, $secretKey);
        
        if (!hash_equals($calculatedHash, $hash)) {
            return false;
        }

        // Проверяем время (данные должны быть не старше 1 дня)
        if (isset($data['auth_date']) && (time() - $data['auth_date']) > 86400) {
            return false;
        }

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