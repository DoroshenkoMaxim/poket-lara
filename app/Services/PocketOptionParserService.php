<?php

namespace App\Services;

use App\Models\Currency;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use DOMDocument;
use DOMXPath;

class PocketOptionParserService
{
    private const POCKET_OPTION_URL = 'https://pocketoption.com/ru/cabinet/try-demo/';
    private const CURRENCIES_SELECTOR = '.assets-block__alist .alist__item';
    
    /**
     * Парсинг валют с PocketOption
     */
    public function parseCurrencies(): array
    {
        try {
            Log::info('Начинаем парсинг валют с PocketOption');
            
            // Получаем HTML страницы
            $response = Http::withOptions([
                'verify' => false,
                'timeout' => 30,
                'headers' => [
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36'
                ]
            ])->get(self::POCKET_OPTION_URL);
            
            if (!$response->successful()) {
                throw new \Exception('Не удалось получить данные с PocketOption: ' . $response->status());
            }
            
            $html = $response->body();
            
            // Парсим HTML
            $currencies = $this->parseHtml($html);
            
            Log::info('Парсинг завершен, найдено валют: ' . count($currencies));
            
            return $currencies;
            
        } catch (\Exception $e) {
            Log::error('Ошибка при парсинге валют: ' . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Парсинг HTML и извлечение данных о валютах
     */
    private function parseHtml(string $html): array
    {
        $currencies = [];
        
        // Создаем DOM документ
        $dom = new DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML($html);
        libxml_clear_errors();
        
        $xpath = new DOMXPath($dom);
        
        // Ищем все элементы валют
        $currencyElements = $xpath->query("//li[contains(@class, 'alist__item')]");
        
        foreach ($currencyElements as $element) {
            try {
                $currency = $this->parseCurrencyElement($element, $xpath);
                if ($currency) {
                    $currencies[] = $currency;
                }
            } catch (\Exception $e) {
                Log::warning('Ошибка при парсинге элемента валюты: ' . $e->getMessage());
                continue;
            }
        }
        
        return $currencies;
    }
    
    /**
     * Парсинг отдельного элемента валюты
     */
    private function parseCurrencyElement($element, DOMXPath $xpath): ?array
    {
        // Получаем название валютной пары
        $labelNodes = $xpath->query(".//span[contains(@class, 'alist__label')]", $element);
        if ($labelNodes->length === 0) {
            return null;
        }
        $label = trim($labelNodes->item(0)->textContent);
        
        // Получаем процент выплаты
        $payoutNodes = $xpath->query(".//span[contains(@class, 'alist__payout')]//span", $element);
        $payout = null;
        $isActive = true;
        
        if ($payoutNodes->length > 0) {
            $payoutText = trim($payoutNodes->item(0)->textContent);
            if (str_contains($payoutText, 'N/A')) {
                $isActive = false;
            } else {
                // Извлекаем число из строки типа "+92%"
                preg_match('/\+?(\d+)%/', $payoutText, $matches);
                if (isset($matches[1])) {
                    $payout = intval($matches[1]);
                }
            }
        }
        
        // Проверяем активность по классам
        $classes = $element->getAttribute('class');
        if (str_contains($classes, 'alist__item--no-active')) {
            $isActive = false;
        }
        
        // Получаем флаги валют
        $flagNodes = $xpath->query(".//span[contains(@class, 'flag-icon')]", $element);
        $flags = [];
        
        foreach ($flagNodes as $flagNode) {
            $flagClass = $flagNode->getAttribute('class');
            if (preg_match('/flag-icon--([a-z]+)/', $flagClass, $matches)) {
                $flags[] = $matches[1];
            }
        }
        
        // Создаем символ валютной пары
        $symbol = $this->createSymbol($label);
        
        return [
            'symbol' => $symbol,
            'label' => $label,
            'payout' => $payout,
            'is_active' => $isActive,
            'is_otc' => str_contains($label, 'OTC'),
            'flags' => $flags
        ];
    }
    
    /**
     * Создание символа валютной пары из названия
     */
    private function createSymbol(string $label): string
    {
        // Убираем " OTC" если есть
        $symbol = str_replace(' OTC', '', $label);
        
        // Заменяем слэш на подчеркивание для использования в качестве ключа
        return str_replace('/', '_', $symbol);
    }
    
    /**
     * Обновление валют в базе данных
     */
    public function updateCurrenciesInDatabase(): int
    {
        try {
            $currencies = $this->parseCurrencies();
            $updatedCount = 0;
            
            foreach ($currencies as $currencyData) {
                Currency::createOrUpdate($currencyData);
                $updatedCount++;
            }
            
            Log::info("Обновлено валют в базе данных: $updatedCount");
            
            return $updatedCount;
            
        } catch (\Exception $e) {
            Log::error('Ошибка при обновлении валют в БД: ' . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Получение статистики валют
     */
    public function getCurrencyStats(): array
    {
        $totalCurrencies = Currency::count();
        $activeCurrencies = Currency::active()->count();
        $otcCurrencies = Currency::otc()->count();
        $lastUpdate = Currency::latest('last_updated')->first()?->last_updated;
        
        return [
            'total' => $totalCurrencies,
            'active' => $activeCurrencies,
            'otc' => $otcCurrencies,
            'last_update' => $lastUpdate
        ];
    }
} 