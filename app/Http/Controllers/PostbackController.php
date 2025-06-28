<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Services\AffiliateService;
use App\Services\TelegramBotService;
use App\Models\User;

class PostbackController extends Controller
{
    protected AffiliateService $affiliateService;
    protected TelegramBotService $telegramBot;

    public function __construct(AffiliateService $affiliateService, TelegramBotService $telegramBot)
    {
        $this->affiliateService = $affiliateService;
        $this->telegramBot = $telegramBot;
    }

    /**
     * Обработать постбек от PocketOption
     */
    public function handlePostback(Request $request): JsonResponse
    {
        try {
            // Логируем входящий запрос
            $logData = [
                'method' => $request->method(),
                'headers' => $request->headers->all(),
                'get' => $request->query->all(),
                'post' => $request->all(),
                'query_string' => $request->getQueryString(),
                'timestamp' => now()->toDateTimeString()
            ];
            
            Log::info('Postback received', $logData);
            
            // Получаем данные из GET запроса
            $data = [
                'click_id' => $request->get('click_id'),
                'trader_id' => $request->get('trader_id'),
                'country' => $request->get('country'),
                'promo' => $request->get('promo') ?? $request->get('code'),
                'device_type' => $request->get('device_type'),
                'os_version' => $request->get('os_version'),
                'browser' => $request->get('browser'),
                'link_type' => $request->get('link_type'),
                'date_time' => $request->get('date_time'),
                'site_id' => $request->get('site_id'),
                'sub_id1' => $request->get('sub_id1'),
                'cid' => $request->get('cid'),
            ];

            $event = $request->get('event', 'reg'); // По умолчанию регистрация

            // Проверяем обязательные параметры
            if (!$data['click_id'] || !$data['trader_id']) {
                return response()->json([
                    'error' => 'click_id and trader_id are required'
                ], 400);
            }

            // Проверяем, что это событие регистрации
            if ($event !== 'reg') {
                return response()->json([
                    'success' => true,
                    'message' => 'Event received but not processed'
                ]);
            }

            // Обрабатываем постбек
            $result = $this->affiliateService->processPostback($data);
            
            // Создаем/находим пользователя и авторизуем его
            $user = $this->createAndLoginUser($result['telegram_id']);
            
            // Формируем ссылку на сигналы (просто главная страница)
            $signalsUrl = url('/');
            
            // Отправляем уведомление пользователю
            $notificationSent = $this->sendRegistrationNotification(
                $result['telegram_id'], 
                $signalsUrl
            );

            Log::info('Postback processed successfully', [
                'click_id' => $data['click_id'],
                'trader_id' => $data['trader_id'],
                'telegram_id' => $result['telegram_id'],
                'user_id' => $user->id,
                'notification_sent' => $notificationSent,
                'signals_url' => $signalsUrl
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Registration processed successfully',
                'click_id' => $data['click_id'],
                'trader_id' => $data['trader_id'],
                'telegram_id' => $result['telegram_id'],
                'user_id' => $user->id,
                'signals_url' => $signalsUrl,
                'notification_sent' => $notificationSent
            ]);

        } catch (\InvalidArgumentException $e) {
            Log::warning('Invalid postback data', [
                'error' => $e->getMessage(),
                'data' => $request->all()
            ]);
            
            return response()->json([
                'error' => $e->getMessage()
            ], 400);
            
        } catch (\Exception $e) {
            Log::error('Postback processing error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'data' => $request->all()
            ]);
            
            if ($e->getMessage() === 'click_id not found in our system') {
                return response()->json([
                    'error' => 'click_id not found in our system'
                ], 404);
            }
            
            if ($e->getMessage() === 'Registration already processed') {
                return response()->json([
                    'success' => true,
                    'message' => 'Registration already processed'
                ]);
            }
            
            return response()->json([
                'error' => 'Internal server error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Создать пользователя и авторизовать его
     */
    private function createAndLoginUser(int $telegramId): User
    {
        // Создаем/находим пользователя
        $user = User::firstOrCreate(
            ['telegram_id' => $telegramId],
            [
                'name' => 'Telegram User ' . $telegramId,
                'email' => 'telegram_' . $telegramId . '@example.com',
                'password' => \Illuminate\Support\Facades\Hash::make(\Illuminate\Support\Str::random(32)),
            ]
        );

        Log::info('User created/found for postback', [
            'user_id' => $user->id,
            'telegram_id' => $telegramId,
            'was_created' => $user->wasRecentlyCreated
        ]);

        return $user;
    }

    /**
     * Отправить уведомление о регистрации
     */
    private function sendRegistrationNotification(int $telegramId, string $signalsUrl): bool
    {
        $message = "🎉 <b>Поздравляем с успешной регистрацией!</b>\n\n";
        $message .= "✅ Ваш аккаунт PocketOption активирован\n";
        $message .= "🎯 Теперь у вас есть доступ к торговым сигналам!\n\n";
        $message .= "🔗 Перейдите по ссылке для просмотра сигналов:\n";
        $message .= $signalsUrl . "\n\n";
        $message .= "💰 Удачной торговли!";

        // Создаем клавиатуру с кнопками
        $keyboard = $this->telegramBot->createInlineKeyboard([
            [
                ['text' => '📊 Перейти к сигналам', 'url' => $signalsUrl]
            ],
            [
                ['text' => '🤖 Открыть бота', 'url' => 'https://t.me/' . config('services.telegram.bot_username')]
            ]
        ]);

        try {
            $result = $this->telegramBot->sendMessage(
                $telegramId,
                $message,
                $keyboard
            );

            return $result && $result['ok'];
        } catch (\Exception $e) {
            Log::error('Failed to send registration notification', [
                'telegram_id' => $telegramId,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
} 