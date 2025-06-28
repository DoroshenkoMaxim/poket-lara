<?php

namespace App;

class TelegramBot
{
    private const API_URL = 'https://api.telegram.org/bot';
    private string $token;
    
    public function __construct(string $token)
    {
        $this->token = $token;
    }
    
    public function sendMessage(int $chatId, string $text, array $keyboard = null): ?array
    {
        $data = [
            'chat_id' => $chatId,
            'text' => $text,
            'parse_mode' => 'HTML'
        ];
        
        if ($keyboard) {
            $data['reply_markup'] = json_encode($keyboard);
        }
        
        return $this->makeRequest('sendMessage', $data);
    }
    
    public function setWebhook(string $url): ?array
    {
        return $this->makeRequest('setWebhook', ['url' => $url]);
    }
    
    public function deleteWebhook(): ?array
    {
        return $this->makeRequest('deleteWebhook');
    }
    
    public function getMe(): ?array
    {
        return $this->makeRequest('getMe');
    }
    
    public function answerCallbackQuery(string $callbackQueryId, string $text = '', bool $showAlert = false): ?array
    {
        return $this->makeRequest('answerCallbackQuery', [
            'callback_query_id' => $callbackQueryId,
            'text' => $text,
            'show_alert' => $showAlert
        ]);
    }
    
    private function makeRequest(string $method, array $data = []): ?array
    {
        $url = self::API_URL . $this->token . '/' . $method;
        
        $options = [
            'http' => [
                'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                'method' => 'POST',
                'content' => http_build_query($data)
            ]
        ];
        
        $context = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
        
        if ($result === false) {
            return null;
        }
        
        return json_decode($result, true);
    }
    
    public function processUpdate(array $update): void
    {
        // Обрабатываем сообщения
        if (isset($update['message'])) {
            $this->processMessage($update['message']);
        }
        
        // Обрабатываем callback queries
        if (isset($update['callback_query'])) {
            $this->processCallbackQuery($update['callback_query']);
        }
    }
    
    private function processMessage(array $message): void
    {
        $chatId = $message['chat']['id'];
        $text = $message['text'] ?? '';
        $userId = $message['from']['id'];
        
        // Логируем сообщение
        $this->logMessage($message);
        
        switch ($text) {
            case '/start':
                $this->handleStartCommand($chatId, $userId);
                break;
                
            default:
                $this->sendMessage($chatId, 
                    "🤖 Привет! Используйте команду /start для получения партнерской ссылки PocketOption.");
                break;
        }
    }
    
    private function handleStartCommand(int $chatId, int $userId): void
    {
        try {
            // Генерируем партнерскую ссылку
            $response = $this->generateAffiliateLink($userId);
            
            if ($response && isset($response['affiliate_link'])) {
                $message = "🎯 <b>Добро пожаловать в PocketOption!</b>\n\n";
                $message .= "🔗 Ваша персональная партнерская ссылка:\n";
                $message .= "<code>" . $response['affiliate_link'] . "</code>\n\n";
                $message .= "📋 <b>Инструкция:</b>\n";
                $message .= "1. Перейдите по ссылке выше\n";
                $message .= "2. Зарегистрируйтесь на PocketOption\n";
                $message .= "3. После регистрации вы получите доступ к торговым сигналам\n\n";
                $message .= "💰 Используйте промокод: <b>WELCOME50</b>\n";
                $message .= "🎁 Получите бонус 50% к депозиту!";
                
                $keyboard = [
                    'inline_keyboard' => [
                        [
                            ['text' => '🚀 Открыть PocketOption', 'url' => $response['affiliate_link']]
                        ]
                    ]
                ];
                
                $this->sendMessage($chatId, $message, $keyboard);
            } else {
                $this->sendMessage($chatId, 
                    "❌ Произошла ошибка при генерации ссылки. Попробуйте позже.");
            }
        } catch (Exception $e) {
            $this->sendMessage($chatId, 
                "❌ Произошла ошибка. Попробуйте позже.");
            error_log('Telegram bot error: ' . $e->getMessage());
        }
    }
    
    private function generateAffiliateLink(int $telegramId): ?array
    {
        $url = "https://" . $_SERVER['HTTP_HOST'] . "/generate_link.php?telegram_id=" . $telegramId . "&format=json";
        
        $response = file_get_contents($url);
        if ($response === false) {
            return null;
        }
        
        return json_decode($response, true);
    }
    
    private function processCallbackQuery(array $callbackQuery): void
    {
        $chatId = $callbackQuery['message']['chat']['id'];
        $data = $callbackQuery['data'] ?? '';
        $callbackQueryId = $callbackQuery['id'];
        
        // Отвечаем на callback query
        $this->answerCallbackQuery($callbackQueryId, 'Обработано!');
    }
    
    private function logMessage(array $message): void
    {
        $logData = [
            'timestamp' => date('Y-m-d H:i:s'),
            'user_id' => $message['from']['id'],
            'chat_id' => $message['chat']['id'],
            'text' => $message['text'] ?? '',
            'username' => $message['from']['username'] ?? null,
            'first_name' => $message['from']['first_name'] ?? null
        ];
        
        file_put_contents(__DIR__ . '/../logs/telegram.log', 
            json_encode($logData) . "\n", FILE_APPEND | LOCK_EX);
    }
    
    public function sendNotificationToUser(int $telegramId, string $message): bool
    {
        $result = $this->sendMessage($telegramId, $message);
        return $result !== null && $result['ok'] === true;
    }
} 