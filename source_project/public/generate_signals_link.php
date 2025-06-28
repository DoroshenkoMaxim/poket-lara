<?php

require_once __DIR__ . '/../autoload.php';

use App\Database;

// Роут для генерации ссылки на сигналы
// GET /generate_signals_link.php?click_id=xxx&trader_id=yyy

header('Content-Type: application/json; charset=utf-8');

try {
    $clickId = $_GET['click_id'] ?? null;
    $traderId = $_GET['trader_id'] ?? null;
    
    if (!$clickId || !$traderId) {
        http_response_code(400);
        echo json_encode(['error' => 'click_id and trader_id are required']);
        exit;
    }
    
    // Проверяем, существует ли связка click_id + trader_id в наших регистрациях  
    $registration = Database::getRegistrationWithTelegram($clickId, $traderId);
    
    if (!$registration) {
        http_response_code(404);
        echo json_encode(['error' => 'Registration not found for this click_id and trader_id combination']);
        exit;
    }
    
    // Генерируем ссылку на сигналы с параметрами для проверки
    $signalsUrl = 'https://googgle.store/signals.php';
    $params = [
        'click_id' => $clickId,
        'trader_id' => $traderId,
        'telegram_id' => $registration['telegram_id'],
        'verified' => 'true'
    ];
    
    $signalsLink = $signalsUrl . '?' . http_build_query($params);
    
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'signals_link' => $signalsLink,
        'click_id' => $clickId,
        'trader_id' => $traderId,
        'telegram_id' => $registration['telegram_id'],
        'registration_data' => [
            'country' => $registration['country'],
            'promo' => $registration['promo'],
            'device_type' => $registration['device_type'],
            'created_at' => $registration['created_at']
        ]
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Internal server error: ' . $e->getMessage()]);
} 