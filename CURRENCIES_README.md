# Система парсинга валют PocketOption

Этот модуль предоставляет функциональность для автоматического парсинга валютных пар с сайта PocketOption и их сохранения в базе данных.

## Компоненты системы

### 1. Модель Currency (`app/Models/Currency.php`)
Модель для работы с валютными парами:
- `symbol` - уникальный символ валютной пары (например, EUR_USD)
- `label` - полное название (например, "EUR/USD OTC")
- `payout` - процент выплаты (например, 92)
- `is_active` - активна ли валютная пара
- `is_otc` - является ли OTC валютой
- `flags` - массив кодов флагов валют
- `last_updated` - время последнего обновления

### 2. Сервис PocketOptionParserService (`app/Services/PocketOptionParserService.php`)
Основной сервис для парсинга:
- `parseCurrencies()` - парсинг HTML страницы
- `updateCurrenciesInDatabase()` - обновление данных в БД
- `getCurrencyStats()` - получение статистики

### 3. Контроллер CurrencyController (`app/Http/Controllers/CurrencyController.php`)
API для работы с валютами:
- Просмотр списка валют с фильтрацией
- Обновление данных из PocketOption
- Получение статистики
- API методы

### 4. Команда Artisan (`app/Console/Commands/UpdateCurrenciesCommand.php`)
Консольная команда для автоматического обновления:
```bash
php artisan currencies:update
php artisan currencies:update --force
```

## Установка и настройка

### 1. Создание таблицы
Выполните миграцию или используйте тестовый маршрут:
```bash
# Через миграцию
php artisan migrate --path=database/migrations/2025_01_02_000000_create_currencies_table.php

# Или через тестовый маршрут
GET /test-currencies
```

### 2. Первоначальное заполнение
```bash
# Через команду
php artisan currencies:update

# Или через API
POST /currencies/update-from-pocket-option
```

## Использование

### Веб-интерфейс
Доступен по адресу: `/currencies`

Возможности:
- Просмотр всех валютных пар
- Фильтрация по статусу, типу, проценту выплаты
- Поиск по названию
- Сортировка
- Обновление данных кнопкой

### API Endpoints

#### Публичные (без авторизации):
```
GET /api/currencies - список активных валют
GET /api/currencies/stats - статистика
GET /api/currencies/best?limit=10&only_otc=true - лучшие валюты
GET /api/currencies/{symbol} - конкретная валюта
```

#### Защищенные (требуют авторизации):
```
POST /api/currencies/update-from-pocket-option - обновление из PocketOption
GET /api/currencies/parse-now - тестовый парсинг
PUT /api/currencies/{symbol} - обновление валюты
```

### Примеры использования API

#### Получить все активные OTC валюты с выплатой выше 80%:
```javascript
fetch('/api/currencies?otc=1&min_payout=80')
  .then(response => response.json())
  .then(data => console.log(data));
```

#### Получить топ-10 лучших валют:
```javascript
fetch('/api/currencies/best?limit=10')
  .then(response => response.json())
  .then(data => console.log(data));
```

#### Обновить данные валют:
```javascript
fetch('/api/currencies/update-from-pocket-option', {
  method: 'POST',
  headers: {
    'Authorization': 'Bearer YOUR_TOKEN',
    'Content-Type': 'application/json'
  }
})
.then(response => response.json())
.then(data => console.log(data));
```

## Автоматизация

### Настройка cron для автоматического обновления
Добавьте в crontab:
```bash
# Обновление каждые 30 минут
*/30 * * * * cd /path/to/laravel && php artisan currencies:update >> /dev/null 2>&1

# Или каждый час
0 * * * * cd /path/to/laravel && php artisan currencies:update
```

### Планировщик Laravel
Добавьте в `app/Console/Kernel.php`:
```php
protected function schedule(Schedule $schedule)
{
    $schedule->command('currencies:update')
             ->everyThirtyMinutes()
             ->withoutOverlapping();
}
```

## Структура данных

### Ответ API списка валют:
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "symbol": "EUR_USD",
      "label": "EUR/USD OTC",
      "payout": 92,
      "is_active": true,
      "is_otc": true,
      "flags": ["eur", "usd"],
      "last_updated": "2025-01-02T10:30:00.000000Z",
      "created_at": "2025-01-02T10:00:00.000000Z",
      "updated_at": "2025-01-02T10:30:00.000000Z"
    }
  ],
  "count": 1
}
```

### Статистика:
```json
{
  "success": true,
  "data": {
    "total": 85,
    "active": 67,
    "otc": 54,
    "last_update": "2025-01-02T10:30:00.000000Z"
  }
}
```

## Обработка ошибок

Сервис включает обработку различных ошибок:
- Недоступность PocketOption
- Изменения в структуре HTML
- Проблемы с сетью
- Ошибки базы данных

Все ошибки логируются в Laravel логи.

## Мониторинг

Для мониторинга работы системы:
1. Проверяйте логи Laravel: `storage/logs/laravel.log`
2. Используйте endpoint статистики: `GET /api/currencies/stats`
3. Настройте алерты на изменение количества активных валют

## Расширение функциональности

### Добавление новых полей
1. Создайте миграцию для добавления поля
2. Обновите модель Currency
3. Модифицируйте PocketOptionParserService для парсинга нового поля

### Интеграция с другими биржами
Создайте аналогичные сервисы для других платформ, используя PocketOptionParserService как образец.

## Тестирование

Для тестирования парсинга используйте:
```
GET /currencies/parse-now
```

Этот endpoint выполняет парсинг без сохранения в БД и возвращает результат.

## Безопасность

- API endpoints для изменения данных защищены middleware авторизации
- Используется rate limiting для предотвращения злоупотреблений
- Все входящие данные валидируются
- SQL инъекции предотвращены использованием Eloquent ORM 