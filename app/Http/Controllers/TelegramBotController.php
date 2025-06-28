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
            
            // Логируем входящее обновление
            Log::info('Telegram webhook received', $update);
            
            $this->processUpdate($update);
            
            return response()->json(['status' => 'ok']);
        } catch (\Exception $e) {
            Log::error('Telegram webhook error: ' . $e->getMessage(), [
                'update' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json(['error' => 'Internal server error'], 500);
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
        $chatId = $message['chat']['id'];
        $text = $message['text'] ?? '';
        $firstName = $message['from']['first_name'] ?? 'Пользователь';

        switch ($text) {
            case '/start':
                $this->handleStartCommand($chatId, $firstName);
                break;
                
            case '/help':
                $this->handleHelpCommand($chatId);
                break;
                
            case '/link':
                $this->handleLinkCommand($chatId);
                break;
                
            default:
                $this->handleUnknownCommand($chatId);
                break;
        }
    }

    /**
     * Обработать команду /start
     */
    private function handleStartCommand(int $chatId, string $firstName): void
    {
        // Генерируем партнерскую ссылку
        $linkData = $this->affiliateService->generateAffiliateLink($chatId);
        
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

        $this->telegramBot->sendMessage($chatId, $message, $keyboard);
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
    private function handleLinkCommand(int $chatId): void
    {
        $linkData = $this->affiliateService->generateAffiliateLink($chatId);
        
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
        $chatId = $callbackQuery['message']['chat']['id'];
        $data = $callbackQuery['data'];

        switch ($data) {
            case 'new_link':
                $this->handleLinkCommand($chatId);
                break;
                
            case 'help':
                $this->handleHelpCommand($chatId);
                break;
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
            'webhook_info' => $webhookInfo
        ]);
    }
} 