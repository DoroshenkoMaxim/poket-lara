<?php

require_once __DIR__ . '/../autoload.php';

use App\Database;

// Роут для генерации партнерской ссылки
// GET /generate_link.php?telegram_id=123456

// Проверяем, нужен ли JSON ответ
$jsonMode = isset($_GET['format']) && $_GET['format'] === 'json';

if ($jsonMode) {
    header('Content-Type: application/json; charset=utf-8');
} else {
    header('Content-Type: text/html; charset=utf-8');
}

try {
    $telegramId = $_GET['telegram_id'] ?? null;
    
    if (!$telegramId) {
        if ($jsonMode) {
            http_response_code(400);
            echo json_encode(['error' => 'telegram_id is required']);
        } else {
            $error = 'Параметр telegram_id обязателен';
        }
        exit;
    }
    
    // Генерируем уникальный click_id
    $clickId = uniqid('click_' . $telegramId . '_', true);
    
    // Сохраняем в JSON файл
    $success = Database::insertAffiliateLink($clickId, (int)$telegramId);
    
    if (!$success) {
        throw new Exception('Failed to save affiliate link');
    }
    
    // Базовая ссылка из документации
    $baseUrl = 'https://u3.shortink.io/register';
    $params = [
        'utm_campaign' => '822453',
        'utm_source' => 'affiliate',
        'utm_medium' => 'sr',
        'a' => 'uCOb8WaCjMsC2U',
        'ac' => 'test',
        'code' => 'WELCOME50',
        'click_id' => $clickId,
        'site_id' => 'telegram_bot'
    ];
    
    $affiliateLink = $baseUrl . '?' . http_build_query($params);
    
    if ($jsonMode) {
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'click_id' => $clickId,
            'affiliate_link' => $affiliateLink,
            'telegram_id' => $telegramId
        ]);
        exit;
    }
    
} catch (Exception $e) {
    if ($jsonMode) {
        http_response_code(500);
        echo json_encode(['error' => 'Internal server error: ' . $e->getMessage()]);
        exit;
    } else {
        $error = 'Ошибка: ' . $e->getMessage();
    }
}

// HTML версия
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Генерация партнерской ссылки</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            color: #333;
        }
        
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .header {
            text-align: center;
            color: white;
            margin-bottom: 30px;
        }
        
        .header h1 {
            font-size: 3rem;
            margin-bottom: 10px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }
        
        .card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        
        .success {
            border-left: 5px solid #4CAF50;
        }
        
        .error {
            border-left: 5px solid #f44336;
        }
        
        .info-section {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin: 20px 0;
        }
        
        .info-title {
            font-size: 1.2rem;
            font-weight: bold;
            color: #333;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .link-display {
            background: #e3f2fd;
            padding: 15px;
            border-radius: 8px;
            margin: 15px 0;
            border-left: 4px solid #2196f3;
            word-break: break-all;
            font-family: 'Courier New', monospace;
        }
        
        .btn {
            display: inline-block;
            background: linear-gradient(45deg, #667eea, #764ba2);
            color: white;
            padding: 12px 25px;
            text-decoration: none;
            border-radius: 25px;
            font-weight: bold;
            transition: transform 0.3s ease;
            margin: 10px 5px;
            border: none;
            cursor: pointer;
        }
        
        .btn:hover {
            transform: scale(1.05);
        }
        
        .btn-secondary {
            background: linear-gradient(45deg, #36d1dc, #5b86e5);
        }
        
        .form-group {
            margin: 20px 0;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }
        
        .form-group input {
            width: 100%;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
        }
        
        .form-group input:focus {
            border-color: #667eea;
            outline: none;
        }
        
        .navigation {
            text-align: center;
            margin-top: 20px;
        }
        
        .error-message {
            color: #c62828;
            background: #ffebee;
            padding: 15px;
            border-radius: 8px;
            margin: 15px 0;
        }
        
        .copy-btn {
            background: #4CAF50;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 12px;
            margin-left: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔗 Генерация партнерской ссылки</h1>
            <p>Создание уникальных ссылок для PocketOption</p>
        </div>

        <?php if (isset($error)): ?>
            <div class="card error">
                <div class="error-message">
                    ❌ <?= htmlspecialchars($error) ?>
                </div>
            </div>
        <?php endif; ?>

        <?php if (!isset($error) && isset($clickId)): ?>
            <div class="card success">
                <h2>✅ Партнерская ссылка создана успешно!</h2>
                
                <div class="info-section">
                    <div class="info-title">📱 Информация о ссылке</div>
                    <p><strong>Telegram ID:</strong> <?= htmlspecialchars($telegramId) ?></p>
                    <p><strong>Click ID:</strong> <?= htmlspecialchars($clickId) ?></p>
                    <p><strong>Время создания:</strong> <?= date('Y-m-d H:i:s') ?></p>
                </div>
                
                <div class="info-section">
                    <div class="info-title">🎯 Партнерская ссылка</div>
                    <div class="link-display">
                        <?= htmlspecialchars($affiliateLink) ?>
                        <button class="copy-btn" onclick="copyToClipboard('<?= htmlspecialchars($affiliateLink) ?>')">📋 Копировать</button>
                    </div>
                    <p><small>💡 Отправьте эту ссылку пользователю для регистрации на PocketOption</small></p>
                </div>
                
                <div class="info-section">
                    <div class="info-title">📊 Мониторинг</div>
                    <p>После регистрации пользователя по ссылке, отслеживайте постбеки:</p>
                    <a href="monitor.php?refresh=on" class="btn btn-secondary">📡 Открыть мониторинг</a>
                    <a href="signals.php?click_id=<?= urlencode($clickId) ?>&trader_id=TRADER_ID" class="btn btn-secondary">🎯 Предпросмотр сигналов</a>
                </div>
            </div>
        <?php endif; ?>

        <div class="card">
            <h3>🚀 Создать новую ссылку</h3>
            <form method="GET">
                <div class="form-group">
                    <label for="telegram_id">Telegram ID пользователя:</label>
                    <input type="number" id="telegram_id" name="telegram_id" value="<?= isset($_GET['telegram_id']) ? htmlspecialchars($_GET['telegram_id']) : '' ?>" placeholder="Введите Telegram ID" required>
                </div>
                <button type="submit" class="btn">🔗 Создать партнерскую ссылку</button>
            </form>
        </div>

        <div class="navigation">
            <a href="test.php" class="btn">🧪 Тестовая страница</a>
            <a href="monitor.php" class="btn">📡 Мониторинг</a>
            <a href="?format=json&telegram_id=<?= urlencode($telegramId ?? '777') ?>" class="btn btn-secondary">📄 JSON API</a>
        </div>
    </div>

    <script>
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(function() {
                alert('Ссылка скопирована в буфер обмена!');
            });
        }
    </script>
</body>
</html> 