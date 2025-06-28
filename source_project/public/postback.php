<?php

require_once __DIR__ . '/../autoload.php';

use App\Database;
use App\TelegramBot;

// Роут для обработки постбеков от PocketPartners
// GET /postback.php с параметрами

header('Content-Type: application/json; charset=utf-8');

try {
    // Логируем входящий запрос для отладки
    $logData = [
        'method' => $_SERVER['REQUEST_METHOD'],
        'headers' => getallheaders(),
        'get' => $_GET,
        'post' => $_POST,
        'query_string' => $_SERVER['QUERY_STRING'] ?? '',
        'timestamp' => date('Y-m-d H:i:s')
    ];
    
    file_put_contents(__DIR__ . '/../logs/postback.log', json_encode($logData) . "\n", FILE_APPEND | LOCK_EX);
    
    // Получаем данные из GET запроса (новый формат с макросами)
    $clickId = $_GET['click_id'] ?? null;
    $traderId = $_GET['trader_id'] ?? null;
    $country = $_GET['country'] ?? null;
    $promo = $_GET['promo'] ?? $_GET['code'] ?? null; // code или promo
    $deviceType = $_GET['device_type'] ?? null;
    $osVersion = $_GET['os_version'] ?? null;
    $browser = $_GET['browser'] ?? null;
    $linkType = $_GET['link_type'] ?? null;
    $dateTime = $_GET['date_time'] ?? null;
    $siteId = $_GET['site_id'] ?? null;
    $subId1 = $_GET['sub_id1'] ?? null;
    $cid = $_GET['cid'] ?? null;
    $event = 'reg'; // Всегда регистрация для GET постбеков
    
    // Проверяем обязательные параметры
    if (!$clickId || !$traderId) {
        http_response_code(400);
        echo json_encode(['error' => 'click_id and trader_id are required']);
        exit;
    }
    
    // Проверяем, что это событие регистрации
    if ($event !== 'reg') {
        // Если это не регистрация, просто подтверждаем получение
        http_response_code(200);
        echo json_encode(['success' => true, 'message' => 'Event received but not processed']);
        exit;
    }
    
    // Проверяем, существует ли такой click_id в наших affiliate_links
    $linkExists = Database::findAffiliateLink($clickId);
    
    if (!$linkExists) {
        http_response_code(404);
        echo json_encode(['error' => 'click_id not found in our system']);
        exit;
    }
    
    // Проверяем, не была ли уже обработана эта регистрация
    $existingRegistration = Database::findRegistration($clickId, $traderId);
    
    if ($existingRegistration) {
        http_response_code(200);
        echo json_encode(['success' => true, 'message' => 'Registration already processed']);
        exit;
    }
    
    // Сохраняем регистрацию со всеми дополнительными данными
    $additionalData = [
        'country' => $country,
        'promo' => $promo,
        'device_type' => $deviceType,
        'os_version' => $osVersion,
        'browser' => $browser,
        'link_type' => $linkType,
        'site_id' => $siteId,
        'sub_id1' => $subId1,  
        'cid' => $cid,
        'date_time' => $dateTime
    ];
    
    $success = Database::insertRegistration($clickId, $traderId, $additionalData);
    
    if (!$success) {
        throw new Exception('Failed to save registration');
    }
    
    // Получаем telegram_id пользователя и создаем временный токен
    $telegramId = $linkExists['telegram_id'];
    $tempToken = Database::createTempToken($telegramId, $clickId, $traderId);
    
    // Формируем ссылку на сигналы с временным токеном
    $signalsUrl = "https://" . $_SERVER['HTTP_HOST'] . "/signals.php?token=" . $tempToken;
    
    // Отправляем уведомление сразу через Telegram Bot
    try {
        $token = '7963957548:AAFC-Z10-eFLWM5ExWErzZATVG8kZZfHqiM';
        $bot = new TelegramBot($token);
        
        $message = "🎉 <b>Поздравляем с успешной регистрацией!</b>\n\n";
        $message .= "✅ Ваш аккаунт PocketOption активирован\n";
        $message .= "🎯 Теперь у вас есть доступ к торговым сигналам!\n\n";
        $message .= "🔗 Перейдите по ссылке для просмотра сигналов:\n";
        $message .= $signalsUrl . "\n\n";
        $message .= "⏰ Ссылка действительна 24 часа\n";
        $message .= "💰 Удачной торговли!";
        
        // Создаем клавиатуру с кнопкой для перехода к сигналам
        $keyboard = [
            'inline_keyboard' => [
                [
                    ['text' => '📊 Перейти к сигналам', 'url' => $signalsUrl]
                ],
                [
                    ['text' => '🤖 Открыть бота', 'url' => 'https://t.me/signallangis_bot']
                ]
            ]
        ];
        
        $result = $bot->sendMessage($telegramId, $message, $keyboard);
        
        // Логируем результат отправки
        file_put_contents(__DIR__ . '/../logs/instant_notifications.log', 
            date('Y-m-d H:i:s') . " - Sent to $telegramId: " . 
            ($result && $result['ok'] ? 'SUCCESS' : 'FAILED') . "\n", FILE_APPEND | LOCK_EX);
        
        // Если отправка не удалась, добавляем в очередь как fallback
        if (!$result || !$result['ok']) {
            Database::addNotificationQueue($telegramId, $message, [
                'click_id' => $clickId,
                'trader_id' => $traderId,
                'signals_url' => $signalsUrl,
                'temp_token' => $tempToken
            ]);
        }
        
    } catch (Exception $e) {
        // В случае ошибки добавляем уведомление в очередь
        error_log('Instant notification failed: ' . $e->getMessage());
        Database::addNotificationQueue($telegramId, $message, [
            'click_id' => $clickId,
            'trader_id' => $traderId,
            'signals_url' => $signalsUrl,
            'temp_token' => $tempToken
        ]);
    }
    
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Registration processed successfully',
        'click_id' => $clickId,
        'trader_id' => $traderId,
        'signals_url' => $signalsUrl
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Internal server error: ' . $e->getMessage()]);
} 