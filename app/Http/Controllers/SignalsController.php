<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\AffiliateService;
use App\Models\User;

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
        $token = $request->get('token');
        $clickId = $request->get('click_id');
        $traderId = $request->get('trader_id');

        // Валидируем доступ
        $accessData = $this->affiliateService->validateSignalsAccess($token, $clickId, $traderId);

        if (!$accessData['access_granted']) {
            return view('signals.access-denied');
        }

        // Если пользователь авторизован через токен, создаем/авторизуем пользователя Laravel
        if ($accessData['method'] === 'token' && !Auth::check()) {
            $user = User::firstOrCreate(
                ['telegram_id' => $accessData['telegram_id']],
                [
                    'name' => 'Telegram User ' . $accessData['telegram_id'],
                    'email' => null,
                    'password' => \Illuminate\Support\Facades\Hash::make(\Illuminate\Support\Str::random(32)),
                ]
            );
            
            Auth::login($user);
        }

        return view('signals.index', [
            'access_data' => $accessData,
            'telegram_login_url' => route('login'),
        ]);
    }

    /**
     * Автоматическая авторизация по токену Sanctum
     */
    public function autoLogin(Request $request)
    {
        $token = $request->get('token');
        
        if (!$token) {
            return redirect()->route('login')->with('error', 'Токен не предоставлен');
        }

        try {
            // Находим пользователя по токену Sanctum
            $personalAccessToken = \Laravel\Sanctum\PersonalAccessToken::findToken($token);
            
            if (!$personalAccessToken) {
                return redirect()->route('login')->with('error', 'Недействительный токен');
            }

            $user = $personalAccessToken->tokenable;
            
            if (!$user) {
                return redirect()->route('login')->with('error', 'Пользователь не найден');
            }

            // Авторизуем пользователя
            Auth::login($user);
            
            // Удаляем использованный токен
            $personalAccessToken->delete();
            
            return redirect()->route('home')->with('success', 'Вы успешно авторизованы через Telegram!');
            
        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', 'Ошибка авторизации: ' . $e->getMessage());
        }
    }
} 