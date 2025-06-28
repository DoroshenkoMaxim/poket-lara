<?php

require_once __DIR__ . '/../autoload.php';

// Главная страница системы PocketOption сигналов

?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PocketOption Сигналы - Главная</title>
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
            margin-bottom: 40px;
        }
        
        .header h1 {
            font-size: 3.5rem;
            margin-bottom: 15px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }
        
        .header p {
            font-size: 1.2rem;
            opacity: 0.9;
            max-width: 600px;
            margin: 0 auto;
            line-height: 1.6;
        }
        
        .features {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            margin-bottom: 40px;
        }
        
        .feature-card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            text-align: center;
            transition: transform 0.3s ease;
        }
        
        .feature-card:hover {
            transform: translateY(-10px);
        }
        
        .feature-icon {
            font-size: 3rem;
            margin-bottom: 20px;
        }
        
        .feature-card h3 {
            color: #667eea;
            margin-bottom: 15px;
            font-size: 1.5rem;
        }
        
        .feature-card p {
            color: #666;
            line-height: 1.6;
        }
        
        .cta-section {
            background: white;
            border-radius: 20px;
            padding: 40px;
            text-align: center;
            box-shadow: 0 15px 40px rgba(0,0,0,0.2);
        }
        
        .cta-button {
            display: inline-block;
            background: linear-gradient(45deg, #667eea, #764ba2);
            color: white;
            padding: 15px 30px;
            border-radius: 25px;
            text-decoration: none;
            font-weight: bold;
            font-size: 1.1rem;
            margin: 10px;
            transition: transform 0.3s ease;
        }
        
        .cta-button:hover {
            transform: scale(1.05);
        }
        
        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin: 40px 0;
        }
        
        .stat-card {
            background: rgba(255,255,255,0.1);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 25px;
            text-align: center;
            color: white;
        }
        
        .stat-number {
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 10px;
        }
        
        .stat-label {
            opacity: 0.8;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🚀 PocketOption Сигналы</h1>
            <p>Профессиональные торговые сигналы для успешной торговли бинарными опционами</p>
        </div>
        
        <div class="stats">
            <div class="stat-card">
                <div class="stat-number">85%</div>
                <div class="stat-label">Точность сигналов</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">24/7</div>
                <div class="stat-label">Работаем круглосуточно</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">1000+</div>
                <div class="stat-label">Довольных клиентов</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">5</div>
                <div class="stat-label">Лет опыта</div>
            </div>
        </div>
        
        <div class="features">
            <div class="feature-card">
                <div class="feature-icon">📊</div>
                <h3>Точные сигналы</h3>
                <p>Высокоточные торговые сигналы с вероятностью успеха до 90%. Профессиональный анализ валютных пар.</p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">⚡</div>
                <h3>Мгновенные уведомления</h3>
                <p>Получайте сигналы в режиме реального времени через Telegram бота сразу после регистрации.</p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">🎯</div>
                <h3>Простота использования</h3>
                <p>Никаких сложных настроек. Зарегистрируйтесь по партнерской ссылке и получите доступ к сигналам.</p>
            </div>
        </div>
        
        <div class="cta-section">
            <h2 style="color: #667eea; margin-bottom: 20px;">Начните зарабатывать уже сегодня!</h2>
            <p style="color: #666; margin-bottom: 30px;">
                Получите партнерскую ссылку в нашем боте, зарегистрируйтесь на PocketOption и получите доступ к премиум сигналам
            </p>
            
            <a href="https://t.me/signallangis_bot" class="cta-button" target="_blank">
                🤖 Получить ссылку в боте
            </a>
            
            <a href="/bot_setup.php" class="cta-button">
                ⚙️ Настройка бота
            </a>
            
            <a href="/test.php" class="cta-button">
                🧪 Тест системы
            </a>
            
            <div style="margin-top: 30px;">
                <h3 style="color: #667eea; margin-bottom: 15px;">Как это работает?</h3>
                <div style="text-align: left; max-width: 600px; margin: 0 auto;">
                    <ol style="color: #666; line-height: 1.8;">
                        <li>Напишите /start боту @signallangis_bot</li>
                        <li>Получите персональную партнерскую ссылку</li>
                        <li>Зарегистрируйтесь на PocketOption по этой ссылке</li>
                        <li>Получите мгновенный доступ к торговым сигналам</li>
                        <li>Начните зарабатывать на точных прогнозах!</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</body>
</html> 