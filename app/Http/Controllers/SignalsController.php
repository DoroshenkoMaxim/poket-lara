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

    /**
     * API: Генерация нового сигнала
     */
    public function generateSignal(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $currencies = [
            'EUR/USD', 'GBP/USD', 'USD/JPY', 'AUD/USD', 'USD/CAD', 
            'EUR/GBP', 'USD/CHF', 'NZD/USD', 'EUR/JPY'
        ];
        
        $timeframes = ['5s', '15s', '30s', '1m', '2m', '5m'];
        
        $selectedCurrency = $request->get('currency');
        $selectedTimeframe = $request->get('timeframe');
        $isMartingale = $request->get('martingale') === 'true';
        $lastSignal = $request->get('last_signal') ? json_decode($request->get('last_signal'), true) : null;

        // Логика генерации сигнала
        if ($isMartingale && $lastSignal && !$lastSignal['result']) {
            // Мартингейл: та же валюта, то же направление, меньший таймфрейм
            $currency = $lastSignal['currency'];
            $direction = $lastSignal['direction'];
            $timeframe = $this->getNextSmallerTimeframe($lastSignal['timeframe'], $timeframes);
        } else {
            // Обычная логика
            $currency = $selectedCurrency ?: $currencies[array_rand($currencies)];
            $timeframe = $selectedTimeframe ?: $timeframes[array_rand($timeframes)];
            $direction = rand(0, 1) ? 'ВВЕРХ' : 'ВНИЗ';
        }

        $signal = [
            'currency' => $currency,
            'timeframe' => $timeframe,
            'direction' => $direction,
            'probability' => rand(70, 99),
            'entry_price' => $this->generatePrice($currency),
            'timestamp' => now()->toISOString(),
        ];

        return response()->json($signal);
    }

    /**
     * API: Получение статистики
     */
    public function getStats(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // В реальном приложении здесь будет получение статистики из базы данных
        // Пока возвращаем рандомные значения для демонстрации
        $currencies = ['EUR/USD', 'GBP/USD', 'USD/JPY', 'AUD/USD', 'USD/CAD', 'EUR/GBP', 'USD/CHF', 'NZD/USD', 'EUR/JPY'];
        $timeframes = ['5s', '15s', '30s', '1m', '2m', '5m'];
        
        $stats = [
            'general' => ['wins' => rand(60, 90), 'total' => rand(80, 120)],
            'martingale' => ['wins' => rand(50, 80), 'total' => rand(70, 100)],
        ];

        foreach ($currencies as $currency) {
            $stats['currency_' . $currency] = [
                'wins' => rand(40, 70),
                'total' => rand(60, 90)
            ];
        }

        foreach ($timeframes as $timeframe) {
            $stats['timeframe_' . $timeframe] = [
                'wins' => rand(45, 75),
                'total' => rand(65, 95)
            ];
        }

        return response()->json($stats);
    }

    /**
     * Получить следующий меньший таймфрейм
     */
    private function getNextSmallerTimeframe($currentTimeframe, $timeframes)
    {
        $index = array_search($currentTimeframe, $timeframes);
        return $index > 0 ? $timeframes[$index - 1] : $timeframes[0];
    }

    /**
     * Сгенерировать цену для валютной пары
     */
    private function generatePrice($currency)
    {
        $prices = [
            'EUR/USD' => 1.0800 + (rand(0, 200) / 10000),
            'GBP/USD' => 1.2500 + (rand(0, 300) / 10000),
            'USD/JPY' => 145.00 + (rand(0, 500) / 100),
            'AUD/USD' => 0.6600 + (rand(0, 200) / 10000),
            'USD/CAD' => 1.3500 + (rand(0, 200) / 10000),
            'EUR/GBP' => 0.8600 + (rand(0, 200) / 10000),
            'USD/CHF' => 0.9100 + (rand(0, 200) / 10000),
            'NZD/USD' => 0.6100 + (rand(0, 200) / 10000),
            'EUR/JPY' => 156.00 + (rand(0, 400) / 100),
        ];

        return isset($prices[$currency]) ? number_format($prices[$currency], 4) : '1.0000';
    }
} 