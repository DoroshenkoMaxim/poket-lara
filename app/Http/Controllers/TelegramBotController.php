<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use App\Services\TelegramBotService;
use App\Services\AffiliateService;

class TelegramBotController extends Controller
{
    protected TelegramBotService $telegramBot;
    protected AffiliateService $affiliateService;

    public function __construct(TelegramBotService $telegramBot, AffiliateService $affiliateService)
    {
        $this->telegramBot = $telegramBot;
        $this->affiliateService = $affiliateService;
    }

    /**
     * Обработать webhook от Telegram
     */
    public function webhook(Request $request): JsonResponse
    {
        try {
            $update = $request->all();
            
            // Детальное логирование входящего webhook
            Log::info('=== TELEGRAM WEBHOOK RECEIVED ===', [
                'timestamp' => now(),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'headers' => $request->headers->all(),
                'raw_input' => $request->getContent(),
                'parsed_update' => $update,
                'request_method' => $request->method(),
            ]);
            
            // Проверяем, что это действительно обновление от Telegram
            if (empty($update)) {
                Log::warning('Empty webhook update received');
                return response()->json(['status' => 'ok', 'message' => 'Empty update']);
            }
            
            // Обрабатываем обновление
            $this->processUpdate($update);
            
            Log::info('Webhook processed successfully');
            return response()->json(['status' => 'ok']);
            
        } catch (\Exception $e) {
            Log::error('=== TELEGRAM WEBHOOK ERROR ===', [
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'update' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Всегда возвращаем 200, чтобы Telegram не повторял запрос
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 200);
        }
    }

    /**
     * Обработать обновление от Telegram
     */
    private function processUpdate(array $update): void
    {
        if (isset($update['message'])) {
            $this->processMessage($update['message']);
        } elseif (isset($update['callback_query'])) {
            $this->processCallbackQuery($update['callback_query']);
        }
    }

    /**
     * Обработать сообщение
     */
    private function processMessage(array $message): void
    {
        $chatId = $message['chat']['id'] ?? null;
        $text = $message['text'] ?? '';
        $userId = $message['from']['id'] ?? null;
        
        // Извлекаем полную информацию о пользователе
        $userInfo = [
            'first_name' => $message['from']['first_name'] ?? null,
            'last_name' => $message['from']['last_name'] ?? null,
            'username' => $message['from']['username'] ?? null,
            'language_code' => $message['from']['language_code'] ?? null,
        ];
        
        Log::info('Processing message', [
            'chat_id' => $chatId,
            'user_id' => $userId,
            'text' => $text,
            'user_info' => $userInfo
        ]);

        if (!$chatId) {
            Log::error('No chat_id in message', ['message' => $message]);
            return;
        }

        $command = trim($text);
        Log::info("Processing command: {$command}");

        switch ($command) {
            case '/start':
                $this->handleStartCommand($chatId, $userInfo);
                break;
                
            case '/help':
                $this->handleHelpCommand($chatId);
                break;
                
            case '/link':
                $this->handleLinkCommand($chatId, $userInfo);
                break;
                
            default:
                $this->handleUnknownCommand($chatId);
                break;
        }
    }

    /**
     * Обработать команду /start
     */
    private function handleStartCommand(int $chatId, array $userInfo): void
    {
        try {
            $firstName = $userInfo['first_name'] ?? 'Пользователь';
            
            Log::info("=== HANDLING /START COMMAND ===", [
                'chat_id' => $chatId,
                'user_info' => $userInfo
            ]);

            // Генерируем партнерскую ссылку с информацией о пользователе
            $linkData = $this->affiliateService->generateAffiliateLink($chatId, $userInfo);
            Log::info("Affiliate link generated", ['link_data' => $linkData]);
            
            $message = "🎉 <b>Добро пожаловать, {$firstName}!</b>\n\n";
            $message .= "🎯 Это бот для получения партнерских ссылок PocketOption и доступа к торговым сигналам.\n\n";
            $message .= "📝 <b>Ваша персональная ссылка для регистрации:</b>\n";
            $message .= $linkData['affiliate_link'] . "\n\n";
            $message .= "✅ <b>Что делать дальше:</b>\n";
            $message .= "1️⃣ Перейдите по ссылке выше\n";
            $message .= "2️⃣ Зарегистрируйтесь на PocketOption\n";
            $message .= "3️⃣ Получите доступ к сигналам автоматически\n\n";
            $message .= "💰 Бонус при регистрации: <b>WELCOME50</b>\n";
            $message .= "⏰ Доступ к сигналам: <b>24 часа</b> после регистрации";

            // Создаем кнопки
            $keyboard = $this->telegramBot->createInlineKeyboard([
                [
                    ['text' => '🚀 Зарегистрироваться', 'url' => $linkData['affiliate_link']]
                ],
                [
                    ['text' => '🔗 Получить новую ссылку', 'callback_data' => 'new_link'],
                    ['text' => '❓ Помощь', 'callback_data' => 'help']
                ]
            ]);

            Log::info("Sending start message", [
                'chat_id' => $chatId,
                'message_length' => strlen($message),
                'keyboard' => $keyboard
            ]);

            $result = $this->telegramBot->sendMessage($chatId, $message, $keyboard);
            
            Log::info("Start message sent", [
                'chat_id' => $chatId,
                'result' => $result
            ]);

        } catch (\Exception $e) {
            Log::error("=== ERROR IN /START COMMAND ===", [
                'chat_id' => $chatId,
                'user_info' => $userInfo,
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Отправляем простое сообщение об ошибке
            try {
                $this->telegramBot->sendMessage($chatId, "❌ Произошла ошибка. Попробуйте еще раз или обратитесь к администратору.");
            } catch (\Exception $fallbackError) {
                Log::error("Failed to send error message", ['error' => $fallbackError->getMessage()]);
            }
        }
    }

    /**
     * Обработать команду /help
     */
    private function handleHelpCommand(int $chatId): void
    {
        $message = "❓ <b>Помощь по боту</b>\n\n";
        $message .= "🤖 <b>Доступные команды:</b>\n";
        $message .= "/start - Получить партнерскую ссылку\n";
        $message .= "/link - Получить новую ссылку\n";
        $message .= "/help - Эта справка\n\n";
        $message .= "📋 <b>Как это работает:</b>\n";
        $message .= "1️⃣ Вы получаете персональную ссылку для регистрации на PocketOption\n";
        $message .= "2️⃣ Регистрируетесь по этой ссылке\n";
        $message .= "3️⃣ Автоматически получаете доступ к торговым сигналам на 24 часа\n";
        $message .= "4️⃣ После истечения токена можете авторизоваться через виджет Telegram\n\n";
        $message .= "💎 <b>Преимущества:</b>\n";
        $message .= "• Бесплатные торговые сигналы\n";
        $message .= "• Бонус WELCOME50 при регистрации\n";
        $message .= "• Круглосуточная поддержка";

        $keyboard = $this->telegramBot->createInlineKeyboard([
            [
                ['text' => '🚀 Получить ссылку', 'callback_data' => 'new_link']
            ]
        ]);

        $this->telegramBot->sendMessage($chatId, $message, $keyboard);
    }

    /**
     * Обработать команду /link
     */
    private function handleLinkCommand(int $chatId, array $userInfo = []): void
    {
        $linkData = $this->affiliateService->generateAffiliateLink($chatId, $userInfo);
        
        $message = "🔗 <b>Новая партнерская ссылка сгенерирована!</b>\n\n";
        $message .= $linkData['affiliate_link'] . "\n\n";
        $message .= "🎯 ID ссылки: <code>{$linkData['click_id']}</code>\n";
        $message .= "💰 Бонус: <b>WELCOME50</b>";

        $keyboard = $this->telegramBot->createInlineKeyboard([
            [
                ['text' => '🚀 Перейти к регистрации', 'url' => $linkData['affiliate_link']]
            ]
        ]);

        $this->telegramBot->sendMessage($chatId, $message, $keyboard);
    }

    /**
     * Обработать неизвестную команду
     */
    private function handleUnknownCommand(int $chatId): void
    {
        $message = "🤔 Я не понимаю эту команду.\n\n";
        $message .= "Используйте /start для получения партнерской ссылки или /help для помощи.";

        $keyboard = $this->telegramBot->createInlineKeyboard([
            [
                ['text' => '🚀 Получить ссылку', 'callback_data' => 'new_link'],
                ['text' => '❓ Помощь', 'callback_data' => 'help']
            ]
        ]);

        $this->telegramBot->sendMessage($chatId, $message, $keyboard);
    }

    /**
     * Обработать callback query
     */
    private function processCallbackQuery(array $callbackQuery): void
    {
        try {
            $chatId = $callbackQuery['message']['chat']['id'] ?? null;
            $data = $callbackQuery['data'] ?? null;
            $callbackQueryId = $callbackQuery['id'] ?? null;
            
            // Извлекаем информацию о пользователе из callback query
            $userInfo = [
                'first_name' => $callbackQuery['from']['first_name'] ?? null,
                'last_name' => $callbackQuery['from']['last_name'] ?? null,
                'username' => $callbackQuery['from']['username'] ?? null,
                'language_code' => $callbackQuery['from']['language_code'] ?? null,
            ];

            Log::info("=== PROCESSING CALLBACK QUERY ===", [
                'chat_id' => $chatId,
                'callback_data' => $data,
                'callback_query_id' => $callbackQueryId,
                'user_info' => $userInfo
            ]);

            if (!$chatId || !$data) {
                Log::error("Missing required callback data", [
                    'chat_id' => $chatId,
                    'data' => $data,
                    'callback_query' => $callbackQuery
                ]);
                return;
            }

            // Отвечаем на callback query (убираем loading)
            if ($callbackQueryId) {
                $this->telegramBot->answerCallbackQuery($callbackQueryId);
            }

            switch ($data) {
                case 'new_link':
                    Log::info("Processing new_link callback");
                    $this->handleLinkCommand($chatId, $userInfo);
                    break;
                    
                case 'help':
                    Log::info("Processing help callback");
                    $this->handleHelpCommand($chatId);
                    break;
                    
                default:
                    Log::warning("Unknown callback data", ['data' => $data]);
                    break;
            }

        } catch (\Exception $e) {
            Log::error("=== ERROR IN CALLBACK QUERY ===", [
                'error' => $e->getMessage(),
                'callback_query' => $callbackQuery,
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Установить webhook
     */
    public function setWebhook(): JsonResponse
    {
        $webhookUrl = url('/telegram/webhook');
        $result = $this->telegramBot->setWebhook($webhookUrl);
        
        if ($result && $result['ok']) {
            return response()->json([
                'success' => true,
                'message' => 'Webhook установлен успешно',
                'url' => $webhookUrl
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка установки webhook',
                'result' => $result
            ], 500);
        }
    }

    /**
     * Получить информацию о боте
     */
    public function getBotInfo(): JsonResponse
    {
        $botInfo = $this->telegramBot->getMe();
        $webhookInfo = $this->telegramBot->getWebhookInfo();
        
        return response()->json([
            'bot_info' => $botInfo,
            'webhook_info' => $webhookInfo,
            'current_domain' => request()->getHost(),
            'expected_webhook_url' => url('/telegram/webhook'),
            'login_auth_url' => url('/telegram/auth'),
        ]);
    }

    /**
     * Переустановить webhook с правильным доменом
     */
    public function reinstallWebhook(): JsonResponse
    {
        // Сначала удаляем старый webhook
        $this->telegramBot->deleteWebhook();
        
        // Устанавливаем новый с текущим доменом
        $webhookUrl = url('/telegram/webhook');
        $result = $this->telegramBot->setWebhook($webhookUrl);
        
        return response()->json([
            'webhook_deleted_and_reinstalled' => true,
            'new_webhook_url' => $webhookUrl,
            'result' => $result,
            'bot_info' => $this->telegramBot->getMe(),
            'webhook_info' => $this->telegramBot->getWebhookInfo(),
        ]);
    }

    /**
     * Полная очистка и переустановка webhook
     */
    public function cleanAndSetupWebhook(): JsonResponse
    {
        try {
            Log::info('=== STARTING WEBHOOK CLEAN AND SETUP ===');
            
            // 1. Получаем текущую информацию
            $currentWebhook = $this->telegramBot->getWebhookInfo();
            Log::info('Current webhook info', ['webhook' => $currentWebhook]);
            
            // 2. Удаляем существующий webhook
            $deleteResult = $this->telegramBot->deleteWebhook();
            Log::info('Delete webhook result', ['result' => $deleteResult]);
            
            // 3. Ждем немного
            sleep(3);
            
            // 4. Устанавливаем новый webhook
            $webhookUrl = url('/telegram/webhook');
            Log::info('Setting new webhook', ['url' => $webhookUrl]);
            
            $setResult = $this->telegramBot->setWebhook($webhookUrl);
            Log::info('Set webhook result', ['result' => $setResult]);
            
            // Если установка не удалась, пробуем еще раз
            if (!$setResult || !isset($setResult['ok']) || !$setResult['ok']) {
                Log::warning('First webhook setup attempt failed, retrying...');
                sleep(2);
                $setResult = $this->telegramBot->setWebhook($webhookUrl);
                Log::info('Second webhook setup attempt', ['result' => $setResult]);
            }
            
            // 5. Проверяем результат
            sleep(2);
            $newWebhook = $this->telegramBot->getWebhookInfo();
            Log::info('New webhook info after setup', ['webhook' => $newWebhook]);
            
            // 6. Получаем информацию о боте
            $botInfo = $this->telegramBot->getMe();
            
            // Проверяем успешность установки
            $success = $setResult && isset($setResult['ok']) && $setResult['ok'];
            $webhookInstalled = $newWebhook && 
                               isset($newWebhook['result']['url']) && 
                               $newWebhook['result']['url'] === $webhookUrl;
            
            Log::info('Webhook setup completed', [
                'success' => $success,
                'webhook_installed' => $webhookInstalled,
                'final_url' => $newWebhook['result']['url'] ?? 'none'
            ]);
            
            return response()->json([
                'success' => $success && $webhookInstalled,
                'message' => $success && $webhookInstalled ? 
                    'Webhook полностью переустановлен' : 
                    'Возможны проблемы с установкой webhook',
                'steps' => [
                    'old_webhook' => $currentWebhook,
                    'delete_result' => $deleteResult,
                    'set_result' => $setResult,
                    'new_webhook' => $newWebhook,
                    'webhook_url_matches' => $webhookInstalled,
                ],
                'bot_info' => $botInfo,
                'webhook_url' => $webhookUrl,
                'test_url' => url('/telegram/test-webhook'),
            ]);
            
        } catch (\Exception $e) {
            Log::error('=== ERROR IN WEBHOOK CLEAN AND SETUP ===', [
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }

    /**
     * Тестовый метод для проверки webhook
     */
    public function testWebhook(): JsonResponse
    {
        try {
            // Отправляем тестовое сообщение в лог
            \Log::info('Webhook test called', [
                'timestamp' => now(),
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Webhook endpoint is working',
                'timestamp' => now(),
                'server_info' => [
                    'php_version' => PHP_VERSION,
                    'laravel_version' => app()->version(),
                    'server_ip' => request()->server('SERVER_ADDR'),
                    'remote_ip' => request()->ip(),
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Проверить webhook через внешний тест
     */
    public function checkWebhookExternal(): JsonResponse
    {
        try {
            $webhookUrl = url('/telegram/webhook');
            
            // Попробуем отправить тестовый POST запрос к самому себе
            $testData = [
                'update_id' => 999999999,
                'message' => [
                    'message_id' => 999999999,
                    'from' => [
                        'id' => 123456789,
                        'first_name' => 'Test',
                        'username' => 'testuser'
                    ],
                    'chat' => [
                        'id' => 123456789,
                        'type' => 'private'
                    ],
                    'date' => time(),
                    'text' => '/test_webhook'
                ]
            ];

            // Используем cURL для тестирования
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $webhookUrl);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($testData));
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'User-Agent: TelegramBot (like TwitterBot)'
            ]);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);
            curl_close($ch);

            return response()->json([
                'success' => $httpCode == 200,
                'webhook_url' => $webhookUrl,
                'http_code' => $httpCode,
                'response' => $response,
                'curl_error' => $error,
                'test_data_sent' => $testData
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }
} 