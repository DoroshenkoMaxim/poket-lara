# Быстрый старт - Парсинг валют PocketOption

## 1. Создание таблицы и тестовые данные
Перейдите по адресу: `http://your-domain.com/test-currencies`

Это создаст таблицу `currencies` и добавит тестовые данные.

## 2. Просмотр валют
Откройте: `http://your-domain.com/currencies`

Здесь вы увидите:
- Статистику валют
- Таблицу с фильтрами
- Кнопки для обновления данных

## 3. Тестирование парсинга
### Через веб-интерфейс:
Нажмите кнопку "Парсинг сейчас" на странице `/currencies`

### Через API:
```bash
curl http://your-domain.com/currencies/parse-now
```

## 4. Обновление валют из PocketOption
### Через веб-интерфейс:
Нажмите кнопку "Обновить валюты" на странице `/currencies`

### Через API:
```bash
curl -X POST http://your-domain.com/currencies/update-from-pocket-option \
  -H "Content-Type: application/json"
```

## 5. API примеры

### Получить все активные валюты:
```bash
curl http://your-domain.com/api/currencies
```

### Получить лучшие валюты:
```bash
curl http://your-domain.com/api/currencies/best?limit=5
```

### Получить статистику:
```bash
curl http://your-domain.com/api/currencies/stats
```

### Получить только OTC валюты с выплатой выше 80%:
```bash
curl "http://your-domain.com/api/currencies?otc=1&min_payout=80"
```

## 6. Автоматизация (опционально)

### Настройка планировщика Laravel:
Добавьте в `app/Console/Kernel.php`:
```php
protected function schedule(Schedule $schedule)
{
    $schedule->command('currencies:update')
             ->everyThirtyMinutes()
             ->withoutOverlapping();
}
```

Затем запустите планировщик:
```bash
php artisan schedule:work
```

## 7. Что парсится с PocketOption

Система извлекает:
- ✅ Название валютной пары (EUR/USD, AUD/CAD и т.д.)
- ✅ Процент выплаты (+92%, +89% и т.д.)
- ✅ Статус активности (активна/неактивна)
- ✅ Тип валюты (OTC или обычная)
- ✅ Флаги стран валют

## 8. Структура базы данных

Таблица `currencies`:
```sql
id              - ID записи
symbol          - Символ валютной пары (EUR_USD)
label           - Полное название (EUR/USD OTC)
payout          - Процент выплаты (92)
is_active       - Активна ли (true/false)
is_otc          - OTC валюта (true/false)
flags           - JSON с кодами флагов ["eur", "usd"]
last_updated    - Время последнего обновления
created_at      - Время создания
updated_at      - Время обновления
```

## Возможные проблемы

### 1. Ошибка "table doesn't exist"
Решение: Откройте `/test-currencies` для создания таблицы

### 2. Ошибка парсинга PocketOption
Возможные причины:
- Блокировка IP
- Изменение структуры сайта
- Проблемы с сетью

### 3. Ошибка "php command not found"
Настройте PATH или используйте полный путь к php.exe

## Мониторинг

Проверяйте логи в `storage/logs/laravel.log` для отслеживания ошибок парсинга. 