<?php

namespace App\Services;

use App\Models\AffiliateLink;
use App\Models\Registration;

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

        return [
            'registration' => $registration,
            'telegram_id' => $affiliateLink->telegram_id,
            'click_id' => $clickId,
            'trader_id' => $traderId
        ];
    }



    /**
     * Проверить, зарегистрирован ли пользователь через нашу систему
     */
    public function isUserRegistered(int $telegramId): bool
    {
        // Проверяем, есть ли пользователь с таким telegram_id в системе
        $user = \App\Models\User::where('telegram_id', $telegramId)->first();
        return $user !== null;
    }
} 