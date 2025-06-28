<?php

namespace App\Services;

use App\Models\AffiliateLink;
use App\Models\Registration;
use App\Models\TempToken;
use App\Models\NotificationQueue;

class AffiliateService
{
    /**
     * Генерировать партнерскую ссылку для пользователя
     */
    public function generateAffiliateLink(int $telegramId): array
    {
        $affiliateLink = AffiliateLink::createLink($telegramId);
        
        // Базовая ссылка из документации PocketOption
        $baseUrl = 'https://u3.shortink.io/register';
        $params = [
            'utm_campaign' => '822453',
            'utm_source' => 'affiliate',
            'utm_medium' => 'sr',
            'a' => 'uCOb8WaCjMsC2U',
            'ac' => 'test',
            'code' => 'WELCOME50',
            'click_id' => $affiliateLink->click_id,
            'site_id' => 'telegram_bot'
        ];
        
        $url = $baseUrl . '?' . http_build_query($params);
        
        return [
            'click_id' => $affiliateLink->click_id,
            'affiliate_link' => $url,
            'telegram_id' => $telegramId
        ];
    }

    /**
     * Обработать постбек от PocketOption
     */
    public function processPostback(array $data): array
    {
        $clickId = $data['click_id'] ?? null;
        $traderId = $data['trader_id'] ?? null;

        if (!$clickId || !$traderId) {
            throw new \InvalidArgumentException('click_id and trader_id are required');
        }

        // Проверяем, существует ли такой click_id
        $affiliateLink = AffiliateLink::findByClickId($clickId);
        if (!$affiliateLink) {
            throw new \Exception('click_id not found in our system');
        }

        // Проверяем, не была ли уже обработана эта регистрация
        $existingRegistration = Registration::findByIds($clickId, $traderId);
        if ($existingRegistration) {
            throw new \Exception('Registration already processed');
        }

        // Создаем регистрацию
        $registration = Registration::create([
            'click_id' => $clickId,
            'trader_id' => $traderId,
            'country' => $data['country'] ?? null,
            'promo' => $data['promo'] ?? $data['code'] ?? null,
            'device_type' => $data['device_type'] ?? null,
            'os_version' => $data['os_version'] ?? null,
            'browser' => $data['browser'] ?? null,
            'link_type' => $data['link_type'] ?? null,
            'site_id' => $data['site_id'] ?? null,
            'sub_id1' => $data['sub_id1'] ?? null,
            'cid' => $data['cid'] ?? null,
            'date_time' => $data['date_time'] ?? null,
        ]);

        // Создаем временный токен
        $tempToken = TempToken::createToken(
            $affiliateLink->telegram_id,
            $clickId,
            $traderId
        );

        // Формируем ссылку на сигналы
        $signalsUrl = url("/signals?token=" . $tempToken->token);

        return [
            'registration' => $registration,
            'temp_token' => $tempToken,
            'signals_url' => $signalsUrl,
            'telegram_id' => $affiliateLink->telegram_id
        ];
    }

    /**
     * Отправить уведомление о регистрации
     */
    public function sendRegistrationNotification(array $postbackData): bool
    {
        $telegramBotService = app(TelegramBotService::class);
        
        $message = "🎉 <b>Поздравляем с успешной регистрацией!</b>\n\n";
        $message .= "✅ Ваш аккаунт PocketOption активирован\n";
        $message .= "🎯 Теперь у вас есть доступ к торговым сигналам!\n\n";
        $message .= "🔗 Перейдите по ссылке для просмотра сигналов:\n";
        $message .= $postbackData['signals_url'] . "\n\n";
        $message .= "⏰ Ссылка действительна 24 часа\n";
        $message .= "💰 Удачной торговли!";

        // Создаем клавиатуру с кнопками
        $keyboard = $telegramBotService->createInlineKeyboard([
            [
                ['text' => '📊 Перейти к сигналам', 'url' => $postbackData['signals_url']]
            ],
            [
                ['text' => '🤖 Открыть бота', 'url' => 'https://t.me/' . config('services.telegram.bot_username')]
            ]
        ]);

        try {
            $result = $telegramBotService->sendMessage(
                $postbackData['telegram_id'],
                $message,
                $keyboard
            );

            if ($result && $result['ok']) {
                return true;
            } else {
                // Добавляем в очередь как fallback
                NotificationQueue::addNotification(
                    $postbackData['telegram_id'],
                    $message,
                    $postbackData
                );
                return false;
            }
        } catch (\Exception $e) {
            // В случае ошибки добавляем в очередь
            NotificationQueue::addNotification(
                $postbackData['telegram_id'],
                $message,
                $postbackData
            );
            return false;
        }
    }

    /**
     * Валидировать токен доступа к сигналам
     */
    public function validateSignalsAccess(string $token = null, string $clickId = null, string $traderId = null): ?array
    {
        // Приоритет: сначала проверяем токен
        if ($token) {
            $tempToken = TempToken::validateToken($token);
            if ($tempToken) {
                return [
                    'access_granted' => true,
                    'method' => 'token',
                    'telegram_id' => $tempToken->telegram_id,
                    'click_id' => $tempToken->click_id,
                    'trader_id' => $tempToken->trader_id,
                    'expires_at' => $tempToken->expires_at,
                ];
            }
        }

        // Если токен не прошел, проверяем прямые параметры
        if ($clickId && $traderId) {
            $registrationData = Registration::getWithTelegram($clickId, $traderId);
            if ($registrationData) {
                return [
                    'access_granted' => true,
                    'method' => 'direct',
                    'telegram_id' => $registrationData['telegram_id'],
                    'click_id' => $clickId,
                    'trader_id' => $traderId,
                ];
            }
        }

        return [
            'access_granted' => false,
            'method' => null,
        ];
    }

    /**
     * Создать временную авторизованную ссылку для Laravel
     */
    public function createAuthenticatedLink(int $telegramId): string
    {
        // Создаем пользователя если его нет
        $user = \App\Models\User::firstOrCreate(
            ['telegram_id' => $telegramId],
            [
                'name' => 'Telegram User ' . $telegramId,
                'email' => null,
                'password' => \Illuminate\Support\Facades\Hash::make(\Illuminate\Support\Str::random(32)),
            ]
        );

        // Создаем временный токен для автоматической авторизации
        $token = $user->createToken('telegram_auth')->plainTextToken;
        
        return url("/auto-login?token=" . $token);
    }
} 