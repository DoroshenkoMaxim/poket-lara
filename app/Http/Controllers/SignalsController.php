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
            'AED/CNY', 'AUD/CAD', 'AUD/CHF', 'AUD/JPY', 'AUD/NZD', 'AUD/USD',
            'BHD/CNY', 'CAD/CHF', 'CAD/JPY', 'CHF/JPY', 'CHF/NOK',
            'EUR/AUD', 'EUR/CAD', 'EUR/CHF', 'EUR/GBP', 'EUR/HUF', 'EUR/JPY', 
            'EUR/NZD', 'EUR/TRY', 'EUR/USD', 'GBP/AUD', 'GBP/CAD', 'GBP/CHF', 
            'GBP/JPY', 'GBP/USD', 'JOD/CNY', 'KES/USD', 'LBP/USD', 'MAD/USD', 
            'NGN/USD', 'NZD/JPY', 'NZD/USD', 'OMR/CNY', 'QAR/CNY', 'SAR/CNY', 
            'TND/USD', 'UAH/USD', 'USD/ARS', 'USD/BDT', 'USD/BRL', 'USD/CAD', 
            'USD/CHF', 'USD/CLP', 'USD/CNH', 'USD/COP', 'USD/DZD', 'USD/EGP', 
            'USD/IDR', 'USD/INR', 'USD/JPY', 'USD/MXN', 'USD/MYR', 'USD/PHP', 
            'USD/PKR', 'USD/SGD', 'USD/THB', 'USD/VND', 'YER/USD', 'ZAR/USD'
        ];
        
        $timeframes = ['5s', '15s', '30s', '1m', '2m', '3m'];
        
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
        $currencies = [
            'AED/CNY', 'AUD/CAD', 'AUD/CHF', 'AUD/JPY', 'AUD/NZD', 'AUD/USD',
            'BHD/CNY', 'CAD/CHF', 'CAD/JPY', 'CHF/JPY', 'CHF/NOK',
            'EUR/AUD', 'EUR/CAD', 'EUR/CHF', 'EUR/GBP', 'EUR/HUF', 'EUR/JPY', 
            'EUR/NZD', 'EUR/TRY', 'EUR/USD', 'GBP/AUD', 'GBP/CAD', 'GBP/CHF', 
            'GBP/JPY', 'GBP/USD', 'JOD/CNY', 'KES/USD', 'LBP/USD', 'MAD/USD', 
            'NGN/USD', 'NZD/JPY', 'NZD/USD', 'OMR/CNY', 'QAR/CNY', 'SAR/CNY', 
            'TND/USD', 'UAH/USD', 'USD/ARS', 'USD/BDT', 'USD/BRL', 'USD/CAD', 
            'USD/CHF', 'USD/CLP', 'USD/CNH', 'USD/COP', 'USD/DZD', 'USD/EGP', 
            'USD/IDR', 'USD/INR', 'USD/JPY', 'USD/MXN', 'USD/MYR', 'USD/PHP', 
            'USD/PKR', 'USD/SGD', 'USD/THB', 'USD/VND', 'YER/USD', 'ZAR/USD'
        ];
        $timeframes = ['5s', '15s', '30s', '1m', '2m', '3m'];
        
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
            // Major Pairs
            'EUR/USD' => 1.0800 + (rand(0, 200) / 10000),
            'GBP/USD' => 1.2500 + (rand(0, 300) / 10000),
            'USD/JPY' => 145.00 + (rand(0, 500) / 100),
            'AUD/USD' => 0.6600 + (rand(0, 200) / 10000),
            'USD/CAD' => 1.3500 + (rand(0, 200) / 10000),
            'USD/CHF' => 0.9100 + (rand(0, 200) / 10000),
            'NZD/USD' => 0.6100 + (rand(0, 200) / 10000),
            
            // Cross Pairs
            'EUR/GBP' => 0.8600 + (rand(0, 200) / 10000),
            'EUR/JPY' => 156.00 + (rand(0, 400) / 100),
            'EUR/CHF' => 0.9800 + (rand(0, 200) / 10000),
            'EUR/AUD' => 1.6200 + (rand(0, 300) / 10000),
            'EUR/CAD' => 1.4600 + (rand(0, 300) / 10000),
            'EUR/NZD' => 1.7500 + (rand(0, 400) / 10000),
            'EUR/TRY' => 29.50 + (rand(0, 1000) / 100),
            'EUR/HUF' => 385.00 + (rand(0, 2000) / 100),
            'GBP/JPY' => 181.00 + (rand(0, 500) / 100),
            'GBP/CHF' => 1.1400 + (rand(0, 200) / 10000),
            'GBP/AUD' => 1.8800 + (rand(0, 400) / 10000),
            'GBP/CAD' => 1.7000 + (rand(0, 300) / 10000),
            'AUD/CAD' => 0.9000 + (rand(0, 200) / 10000),
            'AUD/CHF' => 0.6000 + (rand(0, 150) / 10000),
            'AUD/JPY' => 95.50 + (rand(0, 300) / 100),
            'AUD/NZD' => 1.0800 + (rand(0, 200) / 10000),
            'CAD/CHF' => 0.6750 + (rand(0, 150) / 10000),
            'CAD/JPY' => 107.00 + (rand(0, 300) / 100),
            'CHF/JPY' => 159.00 + (rand(0, 400) / 100),
            'CHF/NOK' => 11.20 + (rand(0, 300) / 100),
            'NZD/JPY' => 88.50 + (rand(0, 300) / 100),
            
            // Exotic and OTC Pairs
            'AED/CNY' => 1.96 + (rand(0, 50) / 1000),
            'BHD/CNY' => 19.15 + (rand(0, 200) / 100),
            'JOD/CNY' => 10.15 + (rand(0, 100) / 100),
            'OMR/CNY' => 18.75 + (rand(0, 200) / 100),
            'QAR/CNY' => 1.98 + (rand(0, 50) / 1000),
            'SAR/CNY' => 1.92 + (rand(0, 50) / 1000),
            'KES/USD' => 0.0067 + (rand(0, 10) / 10000),
            'LBP/USD' => 0.0000666 + (rand(0, 5) / 100000),
            'MAD/USD' => 0.1000 + (rand(0, 20) / 10000),
            'NGN/USD' => 0.0012 + (rand(0, 5) / 10000),
            'TND/USD' => 0.3200 + (rand(0, 50) / 10000),
            'UAH/USD' => 0.0270 + (rand(0, 20) / 10000),
            'YER/USD' => 0.0040 + (rand(0, 5) / 10000),
            'ZAR/USD' => 0.0530 + (rand(0, 30) / 10000),
            'USD/ARS' => 850.00 + (rand(0, 5000) / 100),
            'USD/BDT' => 110.50 + (rand(0, 300) / 100),
            'USD/BRL' => 5.15 + (rand(0, 200) / 100),
            'USD/CLP' => 970.00 + (rand(0, 3000) / 100),
            'USD/CNH' => 7.20 + (rand(0, 100) / 100),
            'USD/COP' => 4300.00 + (rand(0, 20000) / 100),
            'USD/DZD' => 135.00 + (rand(0, 500) / 100),
            'USD/EGP' => 31.00 + (rand(0, 200) / 100),
            'USD/IDR' => 15800.00 + (rand(0, 50000) / 100),
            'USD/INR' => 83.20 + (rand(0, 300) / 100),
            'USD/MXN' => 18.50 + (rand(0, 200) / 100),
            'USD/MYR' => 4.70 + (rand(0, 100) / 100),
            'USD/PHP' => 56.50 + (rand(0, 200) / 100),
            'USD/PKR' => 285.00 + (rand(0, 1000) / 100),
            'USD/SGD' => 1.36 + (rand(0, 50) / 1000),
            'USD/THB' => 36.80 + (rand(0, 200) / 100),
            'USD/VND' => 24300.00 + (rand(0, 100000) / 100),
        ];

        return isset($prices[$currency]) ? number_format($prices[$currency], 4) : '1.0000';
    }
} 