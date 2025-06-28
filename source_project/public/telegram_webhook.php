<?php

require_once __DIR__ . '/../autoload.php';

use App\TelegramBot;

// Webhook endpoint для Telegram бота
header('Content-Type: application/json');

try {
    $token = '7963957548:AAFC-Z10-eFLWM5ExWErzZATVG8kZZfHqiM';
    $bot = new TelegramBot($token);
    
    // Получаем входящие данные
    $input = file_get_contents('php://input');
    $update = json_decode($input, true);
    
    // Логируем все входящие обновления
    file_put_contents(__DIR__ . '/../logs/telegram_webhook.log', 
        date('Y-m-d H:i:s') . ' - ' . $input . "\n", FILE_APPEND | LOCK_EX);
    
    if ($update) {
        $bot->processUpdate($update);
    }
    
    http_response_code(200);
    echo json_encode(['status' => 'ok']);
    
} catch (Exception $e) {
    error_log('Telegram webhook error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Internal server error']);
} 