<?php

require_once __DIR__ . '/../autoload.php';

use App\Database;

// Страница с сигналами
// Поддерживает доступ через:
// 1. Временный токен: /signals.php?token=xxx
// 2. Параметры регистрации: /signals.php?click_id=xxx&trader_id=yyy (без авторизации)

$token = $_GET['token'] ?? null;
$clickId = $_GET['click_id'] ?? null;
$traderId = $_GET['trader_id'] ?? null;

$isVerified = false;
$userInfo = null;
$accessMethod = 'none';

try {
    // Способ 1: Доступ через временный токен (приоритетный)
    if ($token) {
        $tokenData = Database::validateTempToken($token);
        if ($tokenData) {
            $isVerified = true;
            $accessMethod = 'token';
            $clickId = $tokenData['click_id'];
            $traderId = $tokenData['trader_id'];
            
            // Получаем полную информацию о регистрации
            $registration = Database::getRegistrationWithTelegram($clickId, $traderId);
            if ($registration) {
                $userInfo = $registration;
            }
        }
    }
    
    // Способ 2: Доступ через click_id + trader_id (без авторизации)
    elseif ($clickId && $traderId) {
        $registration = Database::getRegistrationWithTelegram($clickId, $traderId);
        
        if ($registration) {
            $isVerified = true;
            $accessMethod = 'direct_params';
            $userInfo = $registration;
        }
    }
    
    // Способ 3: Если нет параметров, показываем инструкцию
    else {
        $accessMethod = 'no_access';
    }
    
} catch (Exception $e) {
    error_log('Signals page error: ' . $e->getMessage());
}

?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Торговые Сигналы - PocketOption</title>
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
        
        .status-card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        
        .verified {
            border-left: 5px solid #4CAF50;
        }
        
        .not-verified {
            border-left: 5px solid #f44336;
        }
        
        .signals-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        
        .signal-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        
        .signal-card:hover {
            transform: translateY(-5px);
        }
        
        .signal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }
        
        .currency-pair {
            font-size: 1.2rem;
            font-weight: bold;
            color: #667eea;
        }
        
        .signal-type.call {
            background: #4CAF50;
            color: white;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.9rem;
        }
        
        .signal-type.put {
            background: #f44336;
            color: white;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.9rem;
        }
        
        .signal-details {
            font-size: 0.9rem;
            color: #666;
        }
        
        .generate-link-btn {
            display: inline-block;
            background: linear-gradient(45deg, #667eea, #764ba2);
            color: white;
            padding: 15px 30px;
            text-decoration: none;
            border-radius: 25px;
            font-weight: bold;
            transition: transform 0.3s ease;
            margin-top: 20px;
        }
        
        .generate-link-btn:hover {
            transform: scale(1.05);
        }
        
        .user-info {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-top: 15px;
        }
        
        .error-message {
            background: #ffebee;
            color: #c62828;
            padding: 15px;
            border-radius: 8px;
            margin-top: 15px;
        }
        
        .access-info {
            background: #e8f5e8;
            color: #2e7d32;
            padding: 15px;
            border-radius: 8px;
            margin-top: 15px;
            border-left: 4px solid #4caf50;
        }
        
        .live-time {
            font-size: 1.2rem;
            font-weight: bold;
            color: white;
            text-align: center;
            margin-bottom: 20px;
            padding: 10px;
            background: rgba(255,255,255,0.1);
            border-radius: 10px;
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0% { opacity: 1; }
            50% { opacity: 0.7; }
            100% { opacity: 1; }
        }
        
        .signal-progress {
            width: 100%;
            height: 20px;
            background: #f0f0f0;
            border-radius: 10px;
            overflow: hidden;
            margin: 10px 0;
            position: relative;
        }
        
        .progress-bar {
            height: 100%;
            background: linear-gradient(90deg, #4CAF50, #66BB6A);
            border-radius: 10px;
            transition: width 1s linear;
            position: relative;
        }
        
        .progress-bar.call {
            background: linear-gradient(90deg, #4CAF50, #66BB6A);
        }
        
        .progress-bar.put {
            background: linear-gradient(90deg, #f44336, #ef5350);
        }
        
        .progress-text {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 0.8rem;
            font-weight: bold;
            color: white;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.5);
        }
        
        .signal-card.active {
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
            border: 2px solid #667eea;
        }
        
        .signal-card.expired {
            opacity: 0.6;
            background: #f5f5f5;
        }
        
        .entry-time {
            font-size: 1.1rem;
            font-weight: bold;
            color: #667eea;
        }
        
        .probability {
            font-size: 1.2rem;
            font-weight: bold;
        }
        
        .probability.high {
            color: #4CAF50;
        }
        
        .probability.medium {
            color: #FF9800;
        }
        
        .probability.low {
            color: #f44336;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🎯 Торговые Сигналы</h1>
            <p>Профессиональные сигналы для PocketOption</p>
        </div>

        <?php if ($isVerified): ?>
            <div class="status-card verified">
                <h2>✅ Доступ подтвержден</h2>
                <p>Добро пожаловать! Ваш аккаунт успешно верифицирован.</p>
                
                <?php if ($accessMethod === 'token'): ?>
                    <div class="access-info">
                        <p><strong>🔐 Доступ по временному токену</strong></p>
                        <p>Ссылка действительна в течение 24 часов</p>
                    </div>
                <?php elseif ($accessMethod === 'direct_params'): ?>
                    <div class="access-info">
                        <p><strong>🔗 Прямой доступ по параметрам</strong></p>
                        <p>Доступ предоставлен через click_id и trader_id</p>
                    </div>
                <?php endif; ?>
                
                <?php if ($userInfo): ?>
                    <div class="user-info">
                        <h3>👤 Информация о пользователе:</h3>
                        <p><strong>Telegram ID:</strong> <?= htmlspecialchars($userInfo['telegram_id']) ?></p>
                        <p><strong>Trader ID:</strong> <?= htmlspecialchars($userInfo['trader_id']) ?></p>
                        <p><strong>Страна:</strong> <?= htmlspecialchars($userInfo['country'] ?: 'Не указана') ?></p>
                        <p><strong>Промокод:</strong> <?= htmlspecialchars($userInfo['promo'] ?: 'Не использован') ?></p>
                        <p><strong>Устройство:</strong> <?= htmlspecialchars($userInfo['device_type'] ?: 'Не указано') ?></p>
                        <p><strong>Регистрация:</strong> <?= htmlspecialchars($userInfo['created_at']) ?></p>
                    </div>
                <?php endif; ?>
            </div>

            <div class="live-time" id="live-time">
                ⏰ Текущее время: <span id="current-time"></span>
            </div>

            <div class="signals-grid" id="signals-grid">
                <!-- Сигналы будут генерироваться динамически через JavaScript -->
            </div>

        <?php elseif ($accessMethod === 'no_access'): ?>
            <div class="status-card not-verified">
                <h2>👋 Добро пожаловать!</h2>
                <p>Для получения доступа к сигналам необходимо использовать правильную ссылку.</p>
                
                <div class="error-message">
                    <h3>🔗 Необходима регистрация через партнерскую ссылку</h3>
                    <p>Для получения доступа к сигналам:</p>
                    <ol style="margin-left: 20px; margin-top: 10px;">
                        <li>Получите партнерскую ссылку в нашем боте @signallangis_bot</li>
                        <li>Зарегистрируйтесь на PocketOption по этой ссылке</li>
                        <li>После регистрации вы получите ссылку на сигналы</li>
                    </ol>
                </div>
                
                <a href="https://t.me/signallangis_bot" class="generate-link-btn" target="_blank">
                    🤖 Перейти к боту
                </a>
            </div>
        
        <?php else: ?>
            <div class="status-card not-verified">
                <h2>❌ Доступ не подтвержден</h2>
                <p>Для получения доступа к сигналам необходимо зарегистрироваться через партнерскую ссылку.</p>
                
                <div class="error-message">
                    <h3>🔗 Нет действительной регистрации</h3>
                    <p>Мы не смогли найти подтвержденную регистрацию для указанных параметров или токен истек.</p>
                </div>
                
                <a href="https://t.me/signallangis_bot" class="generate-link-btn" target="_blank">
                    🚀 Получить партнерскую ссылку в боте
                </a>
            </div>
        <?php endif; ?>
    </div>

    <script>
        <?php if ($isVerified): ?>
        
        // Массив валютных пар
        const currencyPairs = [
            'EUR/USD', 'GBP/USD', 'USD/JPY', 'AUD/USD', 'USD/CAD', 
            'EUR/GBP', 'USD/CHF', 'NZD/USD', 'EUR/JPY', 'GBP/JPY'
        ];
        
        // Массив для хранения активных сигналов
        let activeSignals = [];
        
        // Обновление времени
        function updateTime() {
            const now = new Date();
            const timeString = now.toLocaleTimeString('ru-RU');
            document.getElementById('current-time').textContent = timeString;
        }
        
        // Генерация случайного сигнала
        function generateSignal() {
            const types = ['call', 'put'];
            const expirations = [1, 3, 5];
            const entryDelays = [1, 2, 3, 5, 8];
            
            return {
                id: Date.now() + Math.random(),
                pair: currencyPairs[Math.floor(Math.random() * currencyPairs.length)],
                type: types[Math.floor(Math.random() * types.length)],
                entryTime: new Date(Date.now() + entryDelays[Math.floor(Math.random() * entryDelays.length)] * 60000),
                expiration: expirations[Math.floor(Math.random() * expirations.length)],
                probability: Math.floor(Math.random() * 30) + 70, // 70-99%
                startTime: Date.now(),
                duration: 180000 // 3 минуты жизни сигнала
            };
        }
        
        // Создание HTML для сигнала
        function createSignalHTML(signal) {
            const probabilityClass = signal.probability >= 85 ? 'high' : 
                                   signal.probability >= 75 ? 'medium' : 'low';
            
            return `
                <div class="signal-card active" data-signal-id="${signal.id}">
                    <div class="signal-header">
                        <span class="currency-pair">${signal.pair}</span>
                        <span class="signal-type ${signal.type}">${signal.type.toUpperCase()}</span>
                    </div>
                    <div class="signal-details">
                        <p><strong>Время входа:</strong> <span class="entry-time">${signal.entryTime.toLocaleTimeString('ru-RU', {hour: '2-digit', minute: '2-digit'})}</span></p>
                        <p><strong>Экспирация:</strong> ${signal.expiration} мин</p>
                        <p><strong>Вероятность:</strong> <span class="probability ${probabilityClass}">${signal.probability}%</span></p>
                        <div class="signal-progress">
                            <div class="progress-bar ${signal.type}" style="width: 100%">
                                <div class="progress-text">100%</div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }
        
        // Обновление прогресс-бара
        function updateProgressBar(signal) {
            const elapsed = Date.now() - signal.startTime;
            const progress = Math.max(0, 100 - (elapsed / signal.duration) * 100);
            
            const signalElement = document.querySelector(`[data-signal-id="${signal.id}"]`);
            if (signalElement) {
                const progressBar = signalElement.querySelector('.progress-bar');
                const progressText = signalElement.querySelector('.progress-text');
                
                progressBar.style.width = progress + '%';
                progressText.textContent = Math.round(progress) + '%';
                
                // Добавляем класс expired если время истекло
                if (progress <= 0) {
                    signalElement.classList.remove('active');
                    signalElement.classList.add('expired');
                }
            }
        }
        
        // Рендер всех сигналов
        function renderSignals() {
            const container = document.getElementById('signals-grid');
            container.innerHTML = activeSignals.map(createSignalHTML).join('');
        }
        
        // Обновление активных сигналов
        function updateSignals() {
            const now = Date.now();
            
            // Удаляем истекшие сигналы
            activeSignals = activeSignals.filter(signal => 
                now - signal.startTime < signal.duration + 5000
            );
            
            // Добавляем новые сигналы если нужно
            while (activeSignals.length < 4) {
                activeSignals.push(generateSignal());
            }
            
            // Обновляем прогресс-бары
            activeSignals.forEach(updateProgressBar);
            
            // Перерендериваем если нужно
            if (activeSignals.filter(s => Date.now() - s.startTime < 1000).length > 0) {
                renderSignals();
            }
        }
        
        // Инициализация
        function init() {
            // Генерируем начальные сигналы
            for (let i = 0; i < 4; i++) {
                const signal = generateSignal();
                signal.startTime = Date.now() - Math.random() * 120000; // Случайное время начала
                activeSignals.push(signal);
            }
            
            renderSignals();
            
            // Запускаем таймеры
            updateTime();
            setInterval(updateTime, 1000);
            setInterval(updateSignals, 1000);
            
            // Добавляем новые сигналы периодически
            setInterval(() => {
                if (Math.random() < 0.3 && activeSignals.length < 6) { // 30% шанс каждые 5 секунд
                    activeSignals.push(generateSignal());
                    renderSignals();
                }
            }, 5000);
        }
        
        // Запуск при загрузке страницы
        document.addEventListener('DOMContentLoaded', init);
        
        <?php endif; ?>
    </script>
</body>
</html> 