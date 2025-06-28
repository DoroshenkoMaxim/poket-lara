# Telegram Bot для PocketOption Сигналов

## 🚀 Обзор системы

Этот проект представляет собой полную систему для работы с партнерскими ссылками PocketOption через Telegram бота с интеграцией торговых сигналов.

### Основные функции:

1. **Telegram бот** (@signallangis_bot) - выдает партнерские ссылки пользователям
2. **Система постбеков** - обрабатывает уведомления о регистрациях от PocketOption
3. **Временные токены** - защищенные ссылки на сигналы
4. **Очередь уведомлений** - надежная доставка сообщений пользователям
5. **Прямой доступ** - доступ к сигналам по параметрам регистрации

## 📋 Установка и настройка

### 1. Настройка Telegram бота

1. Перейдите на `/bot_setup.php` в браузере
2. Нажмите "Установить Webhook"
3. Проверьте информацию о боте

### 2. Настройка cron job (опционально)

**ВАЖНО:** Уведомления теперь отправляются мгновенно! Cron job больше не требуется.

Опционально можете добавить fallback cron job (каждые 5 минут):
```bash
*/5 * * * * cd /path/to/your/project && php public/send_notifications.php >> logs/cron.log 2>&1
```

## 🔄 Как работает система

### Схема работы:

```
Пользователь в Telegram
       ↓ /start
Бот выдает партнерскую ссылку
       ↓ переход по ссылке
Регистрация на PocketOption
       ↓ постбек (мгновенно!)
Система создает токен + отправляет уведомление
       ↓ получение ссылки на сигналы
Доступ к торговым сигналам
```

### Детальный процесс:

1. **Пользователь пишет /start в бот**
   - Генерируется уникальный `click_id`
   - Создается запись в `affiliate_links.json`
   - Формируется партнерская ссылка PocketOption
   - Отправляется пользователю

2. **Пользователь регистрируется на PocketOption**
   - PocketOption отправляет постбек на `/postback.php`
   - Система проверяет `click_id` и сохраняет `trader_id`
   - Создается временный токен доступа (24 часа)
   - Формируется ссылка на сигналы с токеном
   - Уведомление добавляется в очередь

3. **Мгновенная отправка уведомления**
   - Сразу после обработки постбека отправляется Telegram сообщение
   - Пользователь получает ссылку на сигналы с красивыми кнопками
   - При сбоях используется fallback через очередь

4. **Доступ к сигналам**
   - Пользователь переходит по ссылке с токеном
   - Система проверяет токен и связку данных
   - Предоставляется доступ к торговым сигналам

## 🔐 Методы доступа к сигналам

Система поддерживает несколько способов доступа к сигналам:

### 1. Временный токен (приоритетный)
```
/signals.php?token=abc123...
```

### 2. Прямые параметры регистрации
```
/signals.php?click_id=xxx&trader_id=yyy
```

### 3. Без параметров
- Показывает инструкции для получения доступа

## 📁 Структура файлов

```
project/
├── src/
│   ├── Database.php           # Работа с JSON базой данных
│   └── TelegramBot.php        # Telegram Bot API
├── public/
│   ├── telegram_webhook.php   # Webhook для бота
│   ├── bot_setup.php         # Управление ботом
│   ├── send_notifications.php # Отправка уведомлений
│   ├── postback.php          # Обработка постбеков
│   ├── generate_link.php     # Генерация ссылок
│   └── signals.php           # Страница с сигналами
├── data/                     # JSON файлы базы данных
├── logs/                     # Файлы логов
└── setup_cron.php           # Настройка cron job
```

## 🗄️ База данных (JSON файлы)

### affiliate_links.json
```json
[
  {
    "id": 1,
    "click_id": "click_123456_unique",
    "telegram_id": 123456789,
    "created_at": "2024-01-01 12:00:00"
  }
]
```

### registrations.json
```json
[
  {
    "id": 1,
    "click_id": "click_123456_unique",
    "trader_id": "TR123456",
    "country": "RU",
    "promo": "WELCOME50",
    "device_type": "mobile",
    "created_at": "2024-01-01 12:30:00"
  }
]
```

### temp_tokens.json
```json
[
  {
    "token": "abc123def456...",
    "telegram_id": 123456789,
    "click_id": "click_123456_unique",
    "trader_id": "TR123456",
    "created_at": "2024-01-01 12:30:00",
    "expires_at": "2024-01-02 12:30:00"
  }
]
```

### notification_queue.json
```json
[
  {
    "id": 1,
    "telegram_id": 123456789,
    "message": "🎉 Поздравляем с регистрацией!...",
    "data": {
      "click_id": "click_123456_unique",
      "trader_id": "TR123456",
      "signals_url": "https://example.com/signals.php?token=..."
    },
    "status": "sent",
    "created_at": "2024-01-01 12:30:00",
    "sent_at": "2024-01-01 12:31:00",
    "attempts": 1
  }
]
```

## 🔧 API Endpoints

### GET /telegram_webhook.php
Webhook для получения обновлений от Telegram

### GET /bot_setup.php
Веб-интерфейс для управления ботом
- `?action=set_webhook` - установить webhook
- `?action=delete_webhook` - удалить webhook
- `?action=get_me` - информация о боте

### GET /send_notifications.php
Отправка уведомлений из очереди (cron job)

### GET /postback.php
Обработка постбеков от PocketOption
Параметры: `click_id`, `trader_id`, `country`, `promo`, и др.

### GET /generate_link.php
Генерация партнерской ссылки
- `telegram_id` - ID пользователя Telegram
- `format=json` - JSON ответ

### GET /signals.php
Страница с торговыми сигналами
- `?token=xxx` - доступ по токену
- `?click_id=xxx&trader_id=yyy` - доступ по параметрам

## 📝 Логирование

Система ведет подробные логи:

- `logs/telegram.log` - сообщения в боте
- `logs/telegram_webhook.log` - webhook обновления
- `logs/instant_notifications.log` - мгновенные уведомления
- `logs/notifications.log` - fallback уведомления
- `logs/postback.log` - постбеки от PocketOption
- `logs/cron.log` - выполнение cron задач (если используется)

## 🔍 Мониторинг и отладка

### Проверка состояния бота:
```bash
curl "https://your-domain.com/bot_setup.php?action=get_me"
```

### Ручная отправка уведомлений:
```bash
php public/send_notifications.php
```

### Просмотр логов:
```bash
tail -f logs/telegram.log
tail -f logs/instant_notifications.log
tail -f logs/postback.log
```

### Проверка cron job:
```bash
crontab -l
tail -f logs/cron.log
```

## 🛡️ Безопасность

1. **Валидация Telegram данных** - проверка подписи через HMAC
2. **Временные токены** - ограниченное время действия (24 часа)
3. **Проверка связки данных** - `click_id + trader_id + telegram_id`
4. **Сессии** - автоматическое истечение через 24 часа
5. **Логирование** - полная история операций

## 🚨 Устранение неполадок

### Бот не отвечает:
1. Проверьте webhook: `/bot_setup.php?action=get_me`
2. Проверьте логи: `logs/telegram_webhook.log`
3. Переустановите webhook

### Уведомления не отправляются:
1. Проверьте логи мгновенных уведомлений: `logs/instant_notifications.log`
2. Проверьте постбеки: `logs/postback.log`
3. Запустите fallback очередь: `php public/send_notifications.php`

### Постбеки не обрабатываются:
1. Проверьте URL постбека в PocketOption
2. Проверьте логи: `logs/postback.log`
3. Протестируйте вручную

### Нет доступа к сигналам:
1. Проверьте токен в базе: `data/temp_tokens.json`
2. Проверьте связку данных: `data/registrations.json`
3. Проверьте параметры запроса

## 📞 Контакты

Bot: @signallangis_bot
Bot Token: 7963957548:AAFC-Z10-eFLWM5ExWErzZATVG8kZZfHqiM

---

## 🎯 Быстрый запуск

1. Откройте `/bot_setup.php` и установите webhook
2. Протестируйте бота: перейдите к @signallangis_bot и отправьте /start
3. Зарегистрируйтесь по полученной ссылке на PocketOption
4. Получите мгновенное уведомление с доступом к сигналам!

**Все готово к работе!** Уведомления отправляются мгновенно при постбеке. 