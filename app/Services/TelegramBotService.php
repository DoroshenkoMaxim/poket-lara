<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramBotService
{
    private string $token;
    private string $apiUrl;

    public function __construct()
    {
        $this->token = config('services.telegram.bot_token');
        $this->apiUrl = "https://api.telegram.org/bot{$this->token}/";
    }

    /**
     * Отправить сообщение
     */
    public function sendMessage(
        int $chatId, 
        string $text, 
        array $keyboard = null, 
        string $parseMode = 'HTML'
    ): ?array {
        $params = [
            'chat_id' => $chatId,
            'text' => $text,
            'parse_mode' => $parseMode,
            'disable_web_page_preview' => true,
        ];

        if ($keyboard) {
            $params['reply_markup'] = json_encode($keyboard);
        }

        return $this->makeRequest('sendMessage', $params);
    }

    /**
     * Отправить фото
     */
    public function sendPhoto(
        int $chatId, 
        string $photo, 
        string $caption = null, 
        array $keyboard = null
    ): ?array {
        $params = [
            'chat_id' => $chatId,
            'photo' => $photo,
            'parse_mode' => 'HTML',
        ];

        if ($caption) {
            $params['caption'] = $caption;
        }

        if ($keyboard) {
            $params['reply_markup'] = json_encode($keyboard);
        }

        return $this->makeRequest('sendPhoto', $params);
    }

    /**
     * Установить webhook
     */
    public function setWebhook(string $url): ?array
    {
        return $this->makeRequest('setWebhook', [
            'url' => $url,
            'allowed_updates' => ['message', 'callback_query'],
        ]);
    }

    /**
     * Удалить webhook
     */
    public function deleteWebhook(): ?array
    {
        return $this->makeRequest('deleteWebhook');
    }

    /**
     * Получить информацию о боте
     */
    public function getMe(): ?array
    {
        return $this->makeRequest('getMe');
    }

    /**
     * Получить информацию о webhook
     */
    public function getWebhookInfo(): ?array
    {
        return $this->makeRequest('getWebhookInfo');
    }

    /**
     * Ответить на callback query
     */
    public function answerCallbackQuery(string $callbackQueryId, string $text = '', bool $showAlert = false): ?array
    {
        $params = [
            'callback_query_id' => $callbackQueryId,
        ];

        if (!empty($text)) {
            $params['text'] = $text;
        }

        if ($showAlert) {
            $params['show_alert'] = true;
        }

        return $this->makeRequest('answerCallbackQuery', $params);
    }

    /**
     * Выполнить запрос к API
     */
    private function makeRequest(string $method, array $params = []): ?array
    {
        try {
            $url = $this->apiUrl . $method;
            
            Log::info("Making Telegram API request", [
                'method' => $method,
                'url' => $url,
                'params' => $params
            ]);
            
            $response = Http::timeout(30)->post($url, $params);
            
            Log::info("Telegram API response", [
                'method' => $method,
                'status' => $response->status(),
                'successful' => $response->successful()
            ]);
            
            if ($response->successful()) {
                $result = $response->json();
                
                Log::info("Telegram API result", [
                    'method' => $method,
                    'result' => $result
                ]);
                
                if ($result && $result['ok']) {
                    return $result;
                } else {
                    Log::error('Telegram API error - result not ok', [
                        'method' => $method,
                        'params' => $params,
                        'response' => $result
                    ]);
                }
            } else {
                Log::error('HTTP error in Telegram request', [
                    'method' => $method,
                    'url' => $url,
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'params' => $params
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Exception in Telegram request', [
                'method' => $method,
                'url' => $this->apiUrl . $method,
                'params' => $params,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }

        return null;
    }

    /**
     * Создать inline клавиатуру
     */
    public function createInlineKeyboard(array $buttons): array
    {
        return [
            'inline_keyboard' => $buttons
        ];
    }

    /**
     * Создать обычную клавиатуру
     */
    public function createReplyKeyboard(array $buttons, bool $oneTime = false, bool $resize = true): array
    {
        return [
            'keyboard' => $buttons,
            'one_time_keyboard' => $oneTime,
            'resize_keyboard' => $resize,
        ];
    }

    /**
     * Удалить клавиатуру
     */
    public function removeKeyboard(): array
    {
        return [
            'remove_keyboard' => true
        ];
    }
} 