<?php

require_once __DIR__ . '/../autoload.php';

use App\TelegramBot;

$token = '7963957548:AAFC-Z10-eFLWM5ExWErzZATVG8kZZfHqiM';
$bot = new TelegramBot($token);

$action = $_GET['action'] ?? '';
$result = null;
$error = null;

if ($action === 'set_webhook') {
    $webhookUrl = "https://" . $_SERVER['HTTP_HOST'] . "/telegram_webhook.php";
    $result = $bot->setWebhook($webhookUrl);
} elseif ($action === 'delete_webhook') {
    $result = $bot->deleteWebhook();
} elseif ($action === 'get_me') {
    $result = $bot->getMe();
}

?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Управление Telegram ботом</title>
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
            padding: 20px;
        }
        
        .container {
            max-width: 800px;
            margin: 0 auto;
        }
        
        .header {
            text-align: center;
            color: white;
            margin-bottom: 30px;
        }
        
        .header h1 {
            font-size: 2.5rem;
            margin-bottom: 10px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }
        
        .card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
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
        
        .btn-success { background: linear-gradient(45deg, #4CAF50, #45a049); }
        .btn-danger { background: linear-gradient(45deg, #f44336, #da190b); }
        .btn-info { background: linear-gradient(45deg, #2196F3, #0b7dda); }
        
        .result {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-top: 15px;
            border-left: 4px solid #28a745;
        }
        
        .error {
            background: #ffebee;
            padding: 15px;
            border-radius: 8px;
            margin-top: 15px;
            border-left: 4px solid #f44336;
            color: #c62828;
        }
        
        pre {
            background: #f8f9fa;
            padding: 10px;
            border-radius: 5px;
            overflow-x: auto;
            font-size: 12px;
        }
        
        .bot-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-top: 15px;
        }
        
        .info-item {
            background: #e3f2fd;
            padding: 10px;
            border-radius: 8px;
            border-left: 4px solid #2196f3;
        }
        
        .info-label {
            font-weight: bold;
            color: #1976d2;
            font-size: 0.9rem;
        }
        
        .info-value {
            margin-top: 5px;
            font-family: monospace;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🤖 Управление Telegram ботом</h1>
            <p>Настройка бота для PocketOption сигналов</p>
        </div>

        <div class="card">
            <h2>🔧 Управление Webhook</h2>
            <p>Webhook URL: <code>https://<?= $_SERVER['HTTP_HOST'] ?>/telegram_webhook.php</code></p>
            
            <div style="margin-top: 20px;">
                <a href="?action=set_webhook" class="btn btn-success">✅ Установить Webhook</a>
                <a href="?action=delete_webhook" class="btn btn-danger">❌ Удалить Webhook</a>
                <a href="?action=get_me" class="btn btn-info">ℹ️ Информация о боте</a>
            </div>
        </div>

        <div class="card">
            <h2>📱 Информация о боте</h2>
            <div class="bot-info">
                <div class="info-item">
                    <div class="info-label">Bot Token</div>
                    <div class="info-value"><?= substr($token, 0, 10) ?>...***</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Bot Username</div>
                    <div class="info-value">@signallangis_bot</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Bot Link</div>
                    <div class="info-value">
                        <a href="https://t.me/signallangis_bot" target="_blank">
                            https://t.me/signallangis_bot
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <?php if ($result): ?>
            <div class="card">
                <h3>✅ Результат операции</h3>
                <div class="result">
                    <pre><?= json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) ?></pre>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="card">
                <h3>❌ Ошибка</h3>
                <div class="error">
                    <?= htmlspecialchars($error) ?>
                </div>
            </div>
        <?php endif; ?>

        <div class="card">
            <h2>📋 Инструкция по использованию</h2>
            <ol style="margin-left: 20px; line-height: 1.6;">
                <li><strong>Установите Webhook</strong> - нажмите кнопку "Установить Webhook"</li>
                <li><strong>Проверьте бота</strong> - перейдите по ссылке <a href="https://t.me/signallangis_bot" target="_blank">@signallangis_bot</a></li>
                <li><strong>Отправьте /start</strong> - бот должен ответить партнерской ссылкой</li>
                <li><strong>Проверьте логи</strong> - все действия записываются в файлы логов</li>
            </ol>
        </div>
    </div>
</body>
</html> 