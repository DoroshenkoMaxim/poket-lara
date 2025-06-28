<?php

namespace App\Http\Controllers;

use App\Models\Currency;
use App\Services\PocketOptionParserService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class CurrencyController extends Controller
{
    protected $parserService;

    public function __construct(PocketOptionParserService $parserService)
    {
        $this->parserService = $parserService;
    }

    /**
     * Показать список валют
     */
    public function index(Request $request): View|JsonResponse
    {
        $query = Currency::query();

        // Фильтрация по активности
        if ($request->has('active')) {
            $query->where('is_active', $request->boolean('active'));
        }

        // Фильтрация по OTC
        if ($request->has('otc')) {
            $query->where('is_otc', $request->boolean('otc'));
        }

        // Фильтрация по минимальному проценту выплаты
        if ($request->has('min_payout')) {
            $query->where('payout', '>=', $request->integer('min_payout'));
        }

        // Поиск по названию
        if ($request->has('search')) {
            $search = $request->string('search');
            $query->where(function ($q) use ($search) {
                $q->where('label', 'like', "%{$search}%")
                  ->orWhere('symbol', 'like', "%{$search}%");
            });
        }

        // Сортировка
        $sortBy = $request->get('sort_by', 'payout');
        $sortDirection = $request->get('sort_direction', 'desc');
        
        if (in_array($sortBy, ['payout', 'label', 'updated_at'])) {
            $query->orderBy($sortBy, $sortDirection);
        }

        $currencies = $query->paginate(50);
        $stats = $this->parserService->getCurrencyStats();

        if ($request->expectsJson()) {
            return response()->json([
                'currencies' => $currencies,
                'stats' => $stats
            ]);
        }

        return view('currencies.index', compact('currencies', 'stats'));
    }

    /**
     * Обновить валюты из PocketOption
     */
    public function updateFromPocketOption(): JsonResponse
    {
        try {
            $updatedCount = $this->parserService->updateCurrenciesInDatabase();
            
            return response()->json([
                'success' => true,
                'message' => "Успешно обновлено {$updatedCount} валют",
                'updated_count' => $updatedCount
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при обновлении валют: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Получить данные валют для API
     */
    public function apiIndex(Request $request): JsonResponse
    {
        $query = Currency::active();

        // Фильтрация по OTC
        if ($request->has('otc')) {
            $query->where('is_otc', $request->boolean('otc'));
        }

        // Фильтрация по минимальному проценту выплаты
        if ($request->has('min_payout')) {
            $query->minPayout($request->integer('min_payout'));
        }

        $currencies = $query->orderBy('payout', 'desc')->get();

        return response()->json([
            'success' => true,
            'data' => $currencies,
            'count' => $currencies->count()
        ]);
    }

    /**
     * Получить статистику валют
     */
    public function stats(): JsonResponse
    {
        $stats = $this->parserService->getCurrencyStats();
        
        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * Получить валюту по символу
     */
    public function show(string $symbol): JsonResponse
    {
        $currency = Currency::where('symbol', $symbol)->first();

        if (!$currency) {
            return response()->json([
                'success' => false,
                'message' => 'Валютная пара не найдена'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $currency
        ]);
    }

    /**
     * Получить лучшие валюты по проценту выплаты
     */
    public function getBest(Request $request): JsonResponse
    {
        $limit = $request->get('limit', 10);
        $onlyOtc = $request->boolean('only_otc', false);

        $query = Currency::active()->whereNotNull('payout');

        if ($onlyOtc) {
            $query->otc();
        }

        $currencies = $query->orderBy('payout', 'desc')
                           ->limit($limit)
                           ->get();

        return response()->json([
            'success' => true,
            'data' => $currencies,
            'count' => $currencies->count()
        ]);
    }

    /**
     * Ручное обновление конкретной валюты
     */
    public function update(Request $request, string $symbol): JsonResponse
    {
        $currency = Currency::where('symbol', $symbol)->first();

        if (!$currency) {
            return response()->json([
                'success' => false,
                'message' => 'Валютная пара не найдена'
            ], 404);
        }

        $request->validate([
            'payout' => 'nullable|integer|min:0|max:100',
            'is_active' => 'boolean'
        ]);

        $currency->update($request->only(['payout', 'is_active']));

        return response()->json([
            'success' => true,
            'message' => 'Валютная пара обновлена',
            'data' => $currency
        ]);
    }

    /**
     * Парсинг в режиме реального времени (для тестирования)
     */
    public function parseNow(): JsonResponse
    {
        try {
            $currencies = $this->parserService->parseCurrencies();
            
            return response()->json([
                'success' => true,
                'message' => 'Парсинг выполнен успешно',
                'data' => $currencies,
                'count' => count($currencies)
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при парсинге: ' . $e->getMessage()
            ], 500);
        }
    }
} 