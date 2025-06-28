<?php

require_once __DIR__ . '/../autoload.php';

use App\Database;

header('Content-Type: text/html; charset=utf-8');

// Функция для чтения последних строк файла
function tail($file, $lines = 50) {
    if (!file_exists($file)) {
        return [];
    }
    
    $data = file($file);
    return array_slice($data, -$lines);
}

// Автообновление каждые 5 секунд
$autoRefresh = isset($_GET['refresh']) && $_GET['refresh'] === 'on';

?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Мониторинг постбеков</title>
    <?php if ($autoRefresh): ?>
    <meta http-equiv="refresh" content="5">
    <?php endif; ?>
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
            max-width: 1200px;
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
        
        .controls {
            text-align: center;
            margin-bottom: 30px;
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
        
        .btn-warning {
            background: linear-gradient(45deg, #f093fb, #f5576c);
        }
        
        .card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        
        .status-card {
            border-left: 5px solid #4CAF50;
        }
        
        .log-section {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin: 20px 0;
        }
        
        .log-section h3 {
            color: #333;
            margin-bottom: 15px;
            font-size: 1.3rem;
        }
        
        .log-entry {
            background: #fff;
            padding: 15px;
            margin: 10px 0;
            border-radius: 8px;
            border-left: 4px solid #2196f3;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .json-data {
            background: #1e1e1e;
            color: #f8f8f2;
            padding: 15px;
            border-radius: 8px;
            overflow-x: auto;
            white-space: pre-wrap;
            font-family: 'Courier New', monospace;
            font-size: 12px;
            margin: 10px 0;
        }
        
        .empty {
            color: #666;
            font-style: italic;
            text-align: center;
            padding: 30px;
            background: #f5f5f5;
            border-radius: 8px;
        }
        
        .timestamp {
            color: #1976d2;
            font-weight: bold;
        }
        
        .success {
            color: #4CAF50;
        }
        
        .error {
            color: #f44336;
        }
        
        .warning {
            color: #ff9800;
        }
        
        .info-badge {
            background: #e3f2fd;
            color: #1976d2;
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 12px;
            margin: 5px;
            display: inline-block;
        }
        
        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin: 20px 0;
        }
        
        .signals-btn {
            background: linear-gradient(45deg, #4CAF50, #45a049);
            color: white;
            padding: 8px 15px;
            text-decoration: none;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            transition: transform 0.3s ease;
            margin: 5px;
            display: inline-block;
        }
        
        .signals-btn:hover {
            transform: scale(1.05);
        }
        
        .signals-btn:disabled {
            background: #ccc;
            cursor: not-allowed;
        }
        
        .postback-actions {
            margin-top: 10px;
            padding-top: 10px;
            border-top: 1px solid #eee;
        }
        
        .copy-success {
            background: #4CAF50;
            color: white;
            font-size: 11px;
            padding: 2px 8px;
            border-radius: 10px;
            margin-left: 5px;
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        
        .copy-success.show {
            opacity: 1;
        }
    </style>
    <script>
        function generateSignalLink(clickId, traderId) {
            if (!clickId || !traderId) {
                alert('Не найдены click_id или trader_id в постбеке');
                return;
            }
            
            const signalUrl = `${window.location.origin}/signals.php?click_id=${encodeURIComponent(clickId)}&trader_id=${encodeURIComponent(traderId)}&verified=true`;
            
            // Копируем в буфер обмена
            navigator.clipboard.writeText(signalUrl).then(function() {
                // Показываем уведомление об успешном копировании
                const button = event.target;
                const successMsg = button.nextElementSibling;
                if (successMsg) {
                    successMsg.classList.add('show');
                    setTimeout(() => {
                        successMsg.classList.remove('show');
                    }, 2000);
                }
            }).catch(function() {
                // Если не удалось скопировать, открываем в новом окне
                window.open(signalUrl, '_blank');
            });
        }
        
        function openSignalPage(clickId, traderId) {
            if (!clickId || !traderId) {
                alert('Не найдены click_id или trader_id в постбеке');
                return;
            }
            
            const signalUrl = `${window.location.origin}/signals.php?click_id=${encodeURIComponent(clickId)}&trader_id=${encodeURIComponent(traderId)}&verified=true`;
            window.open(signalUrl, '_blank');
        }
    </script>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📡 Мониторинг постбеков</h1>
            <p>Отслеживание в реальном времени</p>
        </div>

        <div class="controls">
            <a href="?refresh=<?= $autoRefresh ? 'off' : 'on' ?>" class="btn <?= $autoRefresh ? 'btn-warning' : 'btn-secondary' ?>">
                <?= $autoRefresh ? '⏸️ Выключить автообновление' : '▶️ Включить автообновление' ?>
            </a>
            <a href="?" onclick="location.reload()" class="btn">🔄 Обновить сейчас</a>
            <a href="test.php" class="btn btn-secondary">🧪 Тестовая страница</a>
            <a href="generate_link.php" class="btn btn-secondary">🔗 Создать ссылку</a>
            
            <?php
            // Ищем последний успешный постбек для быстрого доступа к сигналам
            $lastClickId = null;
            $lastTraderId = null;
            
            $logFile = __DIR__ . '/../logs/postback.log';
            if (file_exists($logFile)) {
                $logLines = tail($logFile, 10);
                foreach (array_reverse($logLines) as $line) {
                    $line = trim($line);
                    if (!empty($line)) {
                        try {
                            $data = json_decode($line, true);
                            if ($data && !empty($data['get']['click_id']) && !empty($data['get']['trader_id'])) {
                                $lastClickId = $data['get']['click_id'];
                                $lastTraderId = $data['get']['trader_id'];
                                break;
                            }
                        } catch (Exception $e) {
                            // Игнорируем ошибки парсинга
                        }
                    }
                }
            }
            
            if ($lastClickId && $lastTraderId): ?>
            <button onclick="openSignalPage('<?= htmlspecialchars($lastClickId, ENT_QUOTES) ?>', '<?= htmlspecialchars($lastTraderId, ENT_QUOTES) ?>')" class="btn" style="background: linear-gradient(45deg, #4CAF50, #45a049);">
                🎯 Последние сигналы
            </button>
            <?php endif; ?>
        </div>

        <div class="card status-card">
            <h3>📊 Статус системы</h3>
            <div class="info-badge">🕐 Время: <?= date('Y-m-d H:i:s') ?></div>
            <?php if ($autoRefresh): ?>
                <div class="info-badge success">✅ Автообновление включено (каждые 5 сек)</div>
            <?php else: ?>
                <div class="info-badge warning">⚠️ Автообновление выключено</div>
            <?php endif; ?>
        </div>

        <div class="card">
            <div class="log-section">
                <h3>📋 Последние постбеки (50 записей)</h3>
                
                <?php
                $logFile = __DIR__ . '/../logs/postback.log';
                if (file_exists($logFile)) {
                    $logLines = tail($logFile, 50);
                    if (!empty($logLines)) {
                        foreach (array_reverse($logLines) as $line) {
                            $line = trim($line);
                            if (!empty($line)) {
                                try {
                                    $data = json_decode($line, true);
                                    if ($data) {
                                        echo '<div class="log-entry">';
                                        echo '<strong class="timestamp">[' . ($data['timestamp'] ?? 'N/A') . ']</strong> ';
                                        echo '<span class="info-badge success">' . ($data['method'] ?? 'N/A') . '</span>';
                                        
                                                                if (!empty($data['get'])) {
                            echo '<h4>📥 GET параметры:</h4>';
                            echo '<div class="json-data">' . json_encode($data['get'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . '</div>';
                        }
                        
                        if (!empty($data['post'])) {
                            echo '<h4>📨 POST данные:</h4>';
                            echo '<div class="json-data">' . json_encode($data['post'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . '</div>';
                        }
                        
                        // Проверяем наличие click_id и trader_id для генерации ссылки на сигналы
                        $clickId = null;
                        $traderId = null;
                        
                        if (!empty($data['get']['click_id'])) {
                            $clickId = $data['get']['click_id'];
                        }
                        if (!empty($data['get']['trader_id'])) {
                            $traderId = $data['get']['trader_id'];
                        }
                        
                        if ($clickId && $traderId) {
                            echo '<div class="postback-actions">';
                            echo '<button onclick="generateSignalLink(\'' . htmlspecialchars($clickId, ENT_QUOTES) . '\', \'' . htmlspecialchars($traderId, ENT_QUOTES) . '\')" class="signals-btn">📋 Копировать ссылку на сигналы</button>';
                            echo '<span class="copy-success">Скопировано!</span>';
                            echo '<button onclick="openSignalPage(\'' . htmlspecialchars($clickId, ENT_QUOTES) . '\', \'' . htmlspecialchars($traderId, ENT_QUOTES) . '\')" class="signals-btn">🎯 Открыть сигналы</button>';
                            echo '</div>';
                        }
                        
                        echo '</div>';
                                    } else {
                                        echo '<div class="log-entry">' . htmlspecialchars($line) . '</div>';
                                    }
                                } catch (Exception $e) {
                                    echo '<div class="log-entry">' . htmlspecialchars($line) . '</div>';
                                }
                            }
                        }
                    } else {
                        echo '<div class="empty">📝 Логи пусты - ожидаем первый постбек</div>';
                    }
                } else {
                    echo '<div class="empty">📂 Файл логов не найден</div>';
                }
                ?>
            </div>
        </div>

        <div class="grid">
            <div class="card">
                <div class="log-section">
                    <h3>📱 Партнерские ссылки</h3>
                    <?php
                    $affiliateLinksFile = __DIR__ . '/../data/affiliate_links.json';
                    if (file_exists($affiliateLinksFile)) {
                        $content = file_get_contents($affiliateLinksFile);
                        $data = json_decode($content, true);
                        if ($data) {
                            echo '<div class="json-data">' . json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . '</div>';
                        } else {
                            echo '<div class="empty">Нет данных</div>';
                        }
                    } else {
                        echo '<div class="empty">Файл не найден</div>';
                    }
                    ?>
                </div>
            </div>

            <div class="card">
                <div class="log-section">
                    <h3>👥 Регистрации</h3>
                    <?php
                    $registrationsFile = __DIR__ . '/../data/registrations.json';
                    if (file_exists($registrationsFile)) {
                        $content = file_get_contents($registrationsFile);
                        $data = json_decode($content, true);
                        if ($data) {
                            echo '<div class="json-data">' . json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . '</div>';
                        } else {
                            echo '<div class="empty">Нет данных</div>';
                        }
                    } else {
                        echo '<div class="empty">Файл не найден</div>';
                    }
                    ?>
                </div>
            </div>
        </div>

        <div class="card">
            <h3>🚀 Инструкция по тестированию</h3>
            <ol style="margin: 20px 0; padding-left: 20px;">
                <li>Получите партнерскую ссылку: <a href="generate_link.php?telegram_id=777" target="_blank" class="btn btn-secondary" style="font-size: 12px; padding: 5px 10px;">🔗 Создать ссылку</a></li>
                <li>Зарегистрируйтесь по полученной ссылке на PocketOption</li>
                <li>Дождитесь постбека (он появится в логах выше)</li>
                <li>Проверьте доступ к сигналам</li>
            </ol>
        </div>
    </div>

</body>
</html> 