<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Services\AffiliateService;

class SignalsController extends Controller
{
    protected AffiliateService $affiliateService;

    public function __construct(AffiliateService $affiliateService)
    {
        $this->affiliateService = $affiliateService;
    }

    /**
     * Показать страницу с торговыми сигналами
     */
    public function show(Request $request)
    {
        // Проверяем на старые токены и редиректим без них (но не трогаем новые auto-login токены)
        if ($request->has('click_id') || $request->has('trader_id')) {
            return redirect()->route('signals')->with('info', 'Старые ссылки больше не работают. Используйте авторизацию через Telegram.');
        }
        
        // Проверяем, авторизован ли пользователь
        if (Auth::check()) {
            $user = Auth::user();
            
            // Проверяем, что у пользователя есть telegram_id (зарегистрирован через нашу систему)
            if ($user->telegram_id) {
                return view('signals.index', [
                    'user' => $user,
                    'telegram_id' => $user->telegram_id,
                    'is_authorized' => true,
                ]);
            }
        }

        // Если пользователь не авторизован или не зарегистрирован через нашу систему
        return view('signals.access-denied', [
            'telegram_login_url' => route('login'),
            'bot_url' => 'https://t.me/' . config('services.telegram.bot_username', 'signallangis_bot'),
        ]);
    }

    /**
     * Автоматическая авторизация по временному токену
     */
    public function autoLogin(Request $request)
    {
        $token = $request->get('token');
        
        if (!$token) {
            return redirect()->route('signals')->with('error', 'Токен не указан');
        }

        // Используем токен для авторизации
        $tokenData = $this->affiliateService->useTokenForAuth($token);
        
        if (!$tokenData) {
            return redirect()->route('signals')->with('error', 'Недействительный или истекший токен');
        }

        // Находим пользователя
        $user = User::where('telegram_id', $tokenData['telegram_id'])->first();
        
        if (!$user) {
            return redirect()->route('signals')->with('error', 'Пользователь не найден');
        }

        // Авторизуем пользователя
        Auth::login($user);
        
        return redirect()->route('signals')->with('success', 'Добро пожаловать! Вы успешно авторизованы.');
    }
} 