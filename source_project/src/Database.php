<?php

namespace App;

class Database
{
    private static string $dataDir;
    
    public static function init(): void
    {
        self::$dataDir = __DIR__ . '/../data/';
        if (!is_dir(self::$dataDir)) {
            mkdir(self::$dataDir, 0755, true);
        }
    }
    
    private static function getFilePath(string $table): string
    {
        self::init();
        return self::$dataDir . $table . '.json';
    }
    
    private static function loadData(string $table): array
    {
        $filePath = self::getFilePath($table);
        if (!file_exists($filePath)) {
            return [];
        }
        
        $content = file_get_contents($filePath);
        return $content ? json_decode($content, true) ?? [] : [];
    }
    
    private static function saveData(string $table, array $data): bool
    {
        $filePath = self::getFilePath($table);
        return file_put_contents($filePath, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), LOCK_EX) !== false;
    }
    
    public static function insertAffiliateLink(string $clickId, int $telegramId): bool
    {
        $data = self::loadData('affiliate_links');
        
        // Проверяем, нет ли уже такого click_id
        foreach ($data as $item) {
            if ($item['click_id'] === $clickId) {
                return false; // Уже существует
            }
        }
        
        $data[] = [
            'id' => count($data) + 1,
            'click_id' => $clickId,
            'telegram_id' => $telegramId,
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        return self::saveData('affiliate_links', $data);
    }
    
    public static function findAffiliateLink(string $clickId): ?array
    {
        $data = self::loadData('affiliate_links');
        
        foreach ($data as $item) {
            if ($item['click_id'] === $clickId) {
                return $item;
            }
        }
        
        return null;
    }
    
    public static function insertRegistration(string $clickId, string $traderId, array $additionalData = []): bool
    {
        $data = self::loadData('registrations');
        
        // Проверяем, нет ли уже такой регистрации
        foreach ($data as $item) {
            if ($item['click_id'] === $clickId && $item['trader_id'] === $traderId) {
                return false; // Уже существует
            }
        }
        
        $registration = [
            'id' => count($data) + 1,
            'click_id' => $clickId,
            'trader_id' => $traderId,
            'country' => $additionalData['country'] ?? null,
            'promo' => $additionalData['promo'] ?? null,
            'device_type' => $additionalData['device_type'] ?? null,
            'os_version' => $additionalData['os_version'] ?? null,
            'browser' => $additionalData['browser'] ?? null,
            'link_type' => $additionalData['link_type'] ?? null,
            'site_id' => $additionalData['site_id'] ?? null,
            'sub_id1' => $additionalData['sub_id1'] ?? null,
            'cid' => $additionalData['cid'] ?? null,
            'date_time' => $additionalData['date_time'] ?? null,
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        $data[] = $registration;
        
        return self::saveData('registrations', $data);
    }
    
    public static function findRegistration(string $clickId, string $traderId): ?array
    {
        $data = self::loadData('registrations');
        
        foreach ($data as $item) {
            if ($item['click_id'] === $clickId && $item['trader_id'] === $traderId) {
                return $item;
            }
        }
        
        return null;
    }
    
    public static function getRegistrationWithTelegram(string $clickId, string $traderId): ?array
    {
        $registration = self::findRegistration($clickId, $traderId);
        if (!$registration) {
            return null;
        }
        
        $affiliateLink = self::findAffiliateLink($clickId);
        if (!$affiliateLink) {
            return null;
        }
        
        return array_merge($registration, [
            'telegram_id' => $affiliateLink['telegram_id']
        ]);
    }

    // Новые методы для работы с временными токенами
    public static function createTempToken(int $telegramId, string $clickId, string $traderId): string
    {
        $data = self::loadData('temp_tokens');
        
        // Генерируем уникальный токен
        $token = bin2hex(random_bytes(32));
        
        $data[] = [
            'token' => $token,
            'telegram_id' => $telegramId,
            'click_id' => $clickId,
            'trader_id' => $traderId,
            'created_at' => date('Y-m-d H:i:s'),
            'expires_at' => date('Y-m-d H:i:s', strtotime('+24 hours'))
        ];
        
        self::saveData('temp_tokens', $data);
        
        return $token;
    }
    
    public static function validateTempToken(string $token): ?array
    {
        $data = self::loadData('temp_tokens');
        
        foreach ($data as $item) {
            if ($item['token'] === $token) {
                // Проверяем, не истек ли токен
                if (strtotime($item['expires_at']) > time()) {
                    return $item;
                }
            }
        }
        
        return null;
    }
    
    public static function cleanExpiredTokens(): void
    {
        $data = self::loadData('temp_tokens');
        $currentTime = time();
        
        $data = array_filter($data, function($item) use ($currentTime) {
            return strtotime($item['expires_at']) > $currentTime;
        });
        
        self::saveData('temp_tokens', array_values($data));
    }

    // Методы для работы с очередью уведомлений
    public static function addNotificationQueue(int $telegramId, string $message, array $data = []): bool
    {
        $notifications = self::loadData('notification_queue');
        
        $notifications[] = [
            'id' => count($notifications) + 1,
            'telegram_id' => $telegramId,
            'message' => $message,
            'data' => $data,
            'status' => 'pending',
            'created_at' => date('Y-m-d H:i:s'),
            'attempts' => 0
        ];
        
        return self::saveData('notification_queue', $notifications);
    }
    
    public static function getPendingNotifications(): array
    {
        $data = self::loadData('notification_queue');
        
        return array_filter($data, function($item) {
            return $item['status'] === 'pending' && $item['attempts'] < 3;
        });
    }
    
    public static function markNotificationSent(int $id): bool
    {
        $data = self::loadData('notification_queue');
        
        foreach ($data as &$item) {
            if ($item['id'] === $id) {
                $item['status'] = 'sent';
                $item['sent_at'] = date('Y-m-d H:i:s');
                break;
            }
        }
        
        return self::saveData('notification_queue', $data);
    }
    
    public static function markNotificationFailed(int $id): bool
    {
        $data = self::loadData('notification_queue');
        
        foreach ($data as &$item) {
            if ($item['id'] === $id) {
                $item['attempts']++;
                if ($item['attempts'] >= 3) {
                    $item['status'] = 'failed';
                }
                $item['last_attempt'] = date('Y-m-d H:i:s');
                break;
            }
        }
        
        return self::saveData('notification_queue', $data);
    }
} 