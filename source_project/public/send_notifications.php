<?php

require_once __DIR__ . '/../autoload.php';

use App\Database;
use App\TelegramBot;

// Скрипт для отправки уведомлений из очереди
// Должен запускаться по cron каждую минуту

header('Content-Type: application/json');

try {
    $token = '7963957548:AAFC-Z10-eFLWM5ExWErzZATVG8kZZfHqiM';
    $bot = new TelegramBot($token);
    
    // Получаем все ожидающие уведомления
    $notifications = Database::getPendingNotifications();
    
    $processed = 0;
    $sent = 0;
    $failed = 0;
    
    foreach ($notifications as $notification) {
        $processed++;
        
        try {
            $success = $bot->sendNotificationToUser(
                $notification['telegram_id'], 
                $notification['message']
            );
            
            if ($success) {
                Database::markNotificationSent($notification['id']);
                $sent++;
                
                // Логируем успешную отправку
                file_put_contents(__DIR__ . '/../logs/notifications.log', 
                    date('Y-m-d H:i:s') . " - SENT to " . $notification['telegram_id'] . 
                    " - ID: " . $notification['id'] . "\n", FILE_APPEND | LOCK_EX);
            } else {
                Database::markNotificationFailed($notification['id']);
                $failed++;
                
                // Логируем неудачную отправку
                file_put_contents(__DIR__ . '/../logs/notifications.log', 
                    date('Y-m-d H:i:s') . " - FAILED to " . $notification['telegram_id'] . 
                    " - ID: " . $notification['id'] . "\n", FILE_APPEND | LOCK_EX);
            }
            
            // Небольшая задержка между отправками
            usleep(100000); // 0.1 секунды
            
        } catch (Exception $e) {
            Database::markNotificationFailed($notification['id']);
            $failed++;
            
            error_log('Notification send error: ' . $e->getMessage());
        }
    }
    
    // Очищаем истекшие токены
    Database::cleanExpiredTokens();
    
    echo json_encode([
        'success' => true,
        'processed' => $processed,
        'sent' => $sent,
        'failed' => $failed,
        'timestamp' => date('Y-m-d H:i:s')
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Internal server error: ' . $e->getMessage(),
        'timestamp' => date('Y-m-d H:i:s')
    ]);
} 