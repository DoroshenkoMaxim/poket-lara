<?php

require_once __DIR__ . '/../autoload.php';

use App\Database;

// Устанавливаем заголовок для корректного отображения UTF-8
header('Content-Type: text/html; charset=utf-8');

?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Тест партнерской системы</title>
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
            padding: 20px;
        }
        
        .container {
            max-width: 1000px;
            margin: 0 auto;
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            overflow: hidden;
        }
        
        .header {
            background: linear-gradient(45deg, #667eea, #764ba2);
            color: white;
            padding: 30px;
            text-align: center;
        }
        
        .header h1 {
            font-size: 2.5rem;
            margin-bottom: 10px;
        }
        
        .content {
            padding: 30px;
        }
        
        .test-section {
            margin-bottom: 30px;
            padding: 20px;
            border-radius: 10px;
            background: #f8f9fa;
        }
        
        .test-title {
            font-size: 1.2rem;
            font-weight: bold;
            color: #333;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .success {
            color: #28a745;
        }
        
        .error {
            color: #dc3545;
        }
        
        .info {
            background: #e3f2fd;
            padding: 15px;
            border-radius: 8px;
            margin: 10px 0;
            border-left: 4px solid #2196f3;
        }
        
        .json-display {
            background: #1e1e1e;
            color: #f8f8f2;
            padding: 20px;
            border-radius: 8px;
            font-family: 'Courier New', monospace;
            overflow-x: auto;
            white-space: pre-wrap;
            margin: 15px 0;
        }
        
        .links {
            background: #fff3cd;
            padding: 20px;
            border-radius: 8px;
            border-left: 4px solid #ffc107;
        }
        
        .links a {
            display: block;
            margin: 5px 0;
            color: #007bff;
            text-decoration: none;
        }
        
        .links a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🧪 Тест партнерской системы</h1>
            <p>Проверка работы с JSON файлами</p>
        </div>
        
        <div class="content">
            <?php
            
            try {
                // Получаем telegram_id из GET параметра или используем дефолтный
                $telegramId = isset($_GET['telegram_id']) ? (int)$_GET['telegram_id'] : 123456789;
                
                // Тест 1: Генерация партнерской ссылки
                echo '<div class="test-section">';
                echo '<div class="test-title">1️⃣ Генерация партнерской ссылки</div>';
                
                $clickId = uniqid('click_' . $telegramId . '_', true);
                
                $success = Database::insertAffiliateLink($clickId, $telegramId);
                
                if ($success) {
                    echo '<div class="success">✅ Партнерская ссылка создана</div>';
                    echo '<div class="info"><strong>Click ID:</strong> ' . htmlspecialchars($clickId) . '</div>';
                } else {
                    echo '<div class="error">❌ Ошибка создания ссылки</div>';
                }
                echo '</div>';
                
                // Тест 2: Поиск партнерской ссылки
                echo '<div class="test-section">';
                echo '<div class="test-title">2️⃣ Поиск партнерской ссылки</div>';
                
                $found = Database::findAffiliateLink($clickId);
                if ($found) {
                    echo '<div class="success">✅ Ссылка найдена</div>';
                    echo '<div class="info"><strong>Telegram ID:</strong> ' . htmlspecialchars($found['telegram_id']) . '</div>';
                } else {
                    echo '<div class="error">❌ Ссылка не найдена</div>';
                }
                echo '</div>';
                
                // Тест 3: Имитация постбека - регистрация
                echo '<div class="test-section">';
                echo '<div class="test-title">3️⃣ Обработка регистрации (имитация постбека)</div>';
                
                $traderId = 'TR_' . rand(10000000, 99999999);
                $country = 'RU';
                $promo = 'WELCOME50';
                $deviceType = 'desktop';
                
                $additionalData = [
                    'country' => $country,
                    'promo' => $promo,
                    'device_type' => $deviceType
                ];
                $success = Database::insertRegistration($clickId, $traderId, $additionalData);
                
                if ($success) {
                    echo '<div class="success">✅ Регистрация сохранена</div>';
                    echo '<div class="info"><strong>Trader ID:</strong> ' . htmlspecialchars($traderId) . '</div>';
                    echo '<div class="info"><strong>Страна:</strong> ' . htmlspecialchars($country) . '</div>';
                    echo '<div class="info"><strong>Промокод:</strong> ' . htmlspecialchars($promo) . '</div>';
                } else {
                    echo '<div class="error">❌ Ошибка сохранения регистрации</div>';
                }
                echo '</div>';
                
                // Тест 4: Поиск регистрации
                echo '<div class="test-section">';
                echo '<div class="test-title">4️⃣ Поиск регистрации</div>';
                
                $registration = Database::findRegistration($clickId, $traderId);
                if ($registration) {
                    echo '<div class="success">✅ Регистрация найдена</div>';
                    echo '<div class="info"><strong>Время регистрации:</strong> ' . htmlspecialchars($registration['created_at']) . '</div>';
                } else {
                    echo '<div class="error">❌ Регистрация не найдена</div>';
                }
                echo '</div>';
                
                // Тест 5: Получение полной информации
                echo '<div class="test-section">';
                echo '<div class="test-title">5️⃣ Получение полной информации</div>';
                
                $fullInfo = Database::getRegistrationWithTelegram($clickId, $traderId);
                if ($fullInfo) {
                    echo '<div class="success">✅ Полная информация получена</div>';
                    echo '<div class="info">';
                    echo '<strong>📱 Telegram ID:</strong> ' . htmlspecialchars($fullInfo['telegram_id']) . '<br>';
                    echo '<strong>👤 Trader ID:</strong> ' . htmlspecialchars($fullInfo['trader_id']) . '<br>';
                    echo '<strong>🌍 Страна:</strong> ' . htmlspecialchars($fullInfo['country']) . '<br>';
                    echo '<strong>🎁 Промокод:</strong> ' . htmlspecialchars($fullInfo['promo']) . '<br>';
                    echo '<strong>💻 Устройство:</strong> ' . htmlspecialchars($fullInfo['device_type']) . '<br>';
                    echo '</div>';
                } else {
                    echo '<div class="error">❌ Полная информация не найдена</div>';
                }
                echo '</div>';
                
                // Показываем содержимое JSON файлов
                echo '<div class="test-section">';
                echo '<div class="test-title">📁 Содержимое JSON файлов</div>';
                
                $affiliateLinksFile = __DIR__ . '/../data/affiliate_links.json';
                $registrationsFile = __DIR__ . '/../data/registrations.json';
                
                if (file_exists($affiliateLinksFile)) {
                    echo '<h4>📄 affiliate_links.json:</h4>';
                    $content = file_get_contents($affiliateLinksFile);
                    $formatted = json_encode(json_decode($content), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
                    echo '<div class="json-display">' . htmlspecialchars($formatted) . '</div>';
                } else {
                    echo '<div class="error">❌ Файл affiliate_links.json не найден</div>';
                }
                
                if (file_exists($registrationsFile)) {
                    echo '<h4>📄 registrations.json:</h4>';
                    $content = file_get_contents($registrationsFile);
                    $formatted = json_encode(json_decode($content), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
                    echo '<div class="json-display">' . htmlspecialchars($formatted) . '</div>';
                } else {
                    echo '<div class="error">❌ Файл registrations.json не найден</div>';
                }
                echo '</div>';
                
                // Ссылки для тестирования
                $currentDomain = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'];
                $basePath = dirname($_SERVER['REQUEST_URI']);
                
                echo '<div class="test-section">';
                echo '<div class="test-title">🔗 Ссылки для тестирования HTTP API</div>';
                echo '<div class="links">';
                echo '<strong>Тестируйте API через эти ссылки:</strong><br><br>';
                echo '<a href="' . $currentDomain . $basePath . '/generate_link.php?telegram_id=' . $telegramId . '" target="_blank">📱 Генерация партнерской ссылки</a>';
                echo '<a href="' . $currentDomain . $basePath . '/generate_signals_link.php?click_id=' . urlencode($clickId) . '&trader_id=' . urlencode($traderId) . '" target="_blank">🎯 Генерация ссылки на сигналы</a>';
                echo '<a href="' . $currentDomain . $basePath . '/signals.php?click_id=' . urlencode($clickId) . '&trader_id=' . urlencode($traderId) . '" target="_blank">📊 Страница с сигналами</a>';
                echo '<br><strong>Для тестирования постбека используйте POST запрос к:</strong><br>';
                echo '<code>' . $currentDomain . $basePath . '/postback.php</code>';
                echo '</div>';
                echo '</div>';
                
            } catch (Exception $e) {
                echo '<div class="test-section">';
                echo '<div class="error">❌ Ошибка выполнения тестов: ' . htmlspecialchars($e->getMessage()) . '</div>';
                echo '</div>';
            }
            
            ?>
        </div>
    </div>
</body>
</html> 