# Партнерская система PocketOption

Система для работы с партнерской программой PocketOption с интеграцией постбеков и телеграм-ботом.

## Особенности

- 🔗 Генерация партнерских ссылок с уникальными click_id
- 📨 Обработка постбеков от PocketPartners (новый формат с макросами)
- 🎯 Страница с торговыми сигналами с проверкой доступа
- 📝 JSON файловое хранилище для простого тестирования
- 🔒 Система безопасности и валидации

## Структура проекта

```
POKET/
├── composer.json          # Зависимости PHP
├── src/
│   └── Database.php      # Класс для работы с БД
├── public/
│   ├── .htaccess         # Настройки Apache
│   ├── generate_link.php # Генерация партнерских ссылок
│   ├── postback.php      # Обработка постбеков
│   ├── generate_signals_link.php # Генерация ссылок на сигналы
│   └── signals.php       # Страница с сигналами
├── data/                 # SQLite база данных
├── logs/                 # Логи системы
└── README.md
```

## Установка

1. Установите зависимости:
```bash
composer install
```

2. Настройте права доступа:
```bash
chmod 755 data/ logs/
chmod 644 data/*.json logs/postback.log
```

3. Тестирование системы:
```bash
php test.php
```

## API Endpoints

### 1. Генерация партнерской ссылки
**GET** `/generate_link.php?telegram_id=123456`

Ответ:
```json
{
    "success": true,
    "click_id": "click_123456_...",
    "affiliate_link": "https://u3.shortink.io/register?...",
    "telegram_id": "123456"
}
```

### 2. Обработка постбека
**POST** `/postback.php`

Параметры (новый формат PocketPartners):
- `click_id` - ID клика
- `trader_id` - ID трейдера
- `country` - Страна
- `promo` - Промокод
- `device_type` - Тип устройства
- `event` - Тип события (reg, conf, ftd, dep)

### 3. Генерация ссылки на сигналы
**GET** `/generate_signals_link.php?click_id=xxx&trader_id=yyy`

### 4. Страница сигналов
**GET** `/signals.php?click_id=xxx&trader_id=yyy&telegram_id=zzz`

## Настройка постбека в PocketPartners

1. URL постбека: `https://yourdomain.com/postback.php`
2. Метод: `POST`
3. Событие: `Trader has registered`
4. URL с макросами:
```
https://yourdomain.com/postback.php?event=reg&click_id={click_id}&trader_id={trader_id}&country={country}&promo={promo}&device_type={device_type}
```

## Алгоритм работы

1. **Генерация ссылки**: Пользователь запрашивает партнерскую ссылку через telegram_id
2. **Регистрация**: Пользователь переходит по ссылке и регистрируется на PocketOption
3. **Постбек**: PocketPartners отправляет постбек с данными регистрации
4. **Связывание**: Система сохраняет связку click_id → trader_id
5. **Доступ к сигналам**: Пользователь получает доступ к торговым сигналам

## Хранилище данных (JSON файлы)

### Файл `data/affiliate_links.json`
```json
[
  {
    "id": 1,
    "click_id": "click_123456_unique_id",
    "telegram_id": 123456789,
    "created_at": "2024-01-01 12:00:00"
  }
]
```

### Файл `data/registrations.json`
```json
[
  {
    "id": 1,
    "click_id": "click_123456_unique_id",
    "trader_id": "TR_12345678",
    "country": "RU",
    "promo": "WELCOME50",
    "device_type": "desktop",
    "created_at": "2024-01-01 12:05:00"
  }
]
```

## Безопасность

- SQL-инъекции защищены через prepared statements
- Валидация всех входящих параметров
- Логирование всех постбеков для отладки
- CORS настройки для кроссдоменных запросов
- Защита от просмотра служебных файлов

## Мониторинг

Все постбеки логируются в `logs/postback.log` в формате JSON для анализа и отладки.

## Требования

- PHP 8.4+
- ext-json
- Apache с mod_rewrite
- Composer

## Тестирование

Запустите тестовый скрипт для проверки функциональности:

```bash
php test.php
```

Пример тестирования через HTTP:

```bash
# 1. Генерация партнерской ссылки
curl "http://localhost/generate_link.php?telegram_id=123456789"

# 2. Имитация постбека
curl -X POST "http://localhost/postback.php" \
  -d "click_id=click_123456_unique_id&trader_id=TR_12345678&country=RU&promo=WELCOME50&device_type=desktop&event=reg"

# 3. Генерация ссылки на сигналы
curl "http://localhost/generate_signals_link.php?click_id=click_123456_unique_id&trader_id=TR_12345678"

# 4. Просмотр сигналов
curl "http://localhost/signals.php?click_id=click_123456_unique_id&trader_id=TR_12345678"
``` 