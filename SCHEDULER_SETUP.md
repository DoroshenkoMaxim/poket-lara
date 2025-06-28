# Настройка автоматического запуска парсинга валют

## 🕛 Запуск каждый день в 00:00

### Способ 1: Laravel планировщик (рекомендуемый)

#### Шаг 1: Настройка уже выполнена
В файле `app/Console/Kernel.php` добавлена задача:
```php
$schedule->command('currencies:update')
         ->dailyAt('00:00')
         ->withoutOverlapping()
         ->runInBackground()
         ->emailOutputOnFailure('admin@example.com');
```

#### Шаг 2: Запуск планировщика

##### Для Windows (OSPanel):

**Вариант A: Через Task Scheduler (рекомендуемый)**
1. Откройте "Планировщик задач" (Task Scheduler)
2. Создайте новую задачу
3. Настройки:
   - **Имя**: Laravel Currency Parser
   - **Триггер**: Ежедневно в 00:00
   - **Действие**: Запустить программу
   - **Программа**: `C:\OSPanel\modules\php\PHP_8.2\php.exe`
   - **Аргументы**: `artisan schedule:run`
   - **Рабочая папка**: `C:\OSPanel\domains\laravel`

**Вариант B: Через командную строку (каждую минуту)**
```cmd
schtasks /create /tn "Laravel Scheduler" /tr "C:\OSPanel\modules\php\PHP_8.2\php.exe artisan schedule:run" /sc minute /mo 1 /f
```

**Вариант C: Постоянно запущенный процесс**
Создайте `scheduler.bat`:
```batch
@echo off
cd /d C:\OSPanel\domains\laravel
C:\OSPanel\modules\php\PHP_8.2\php.exe artisan schedule:work
```

##### Для Linux:

**Добавьте в crontab:**
```bash
crontab -e
```

Добавьте строку:
```bash
* * * * * cd /path/to/laravel && php artisan schedule:run >> /dev/null 2>&1
```

#### Шаг 3: Проверка работы

**Тестирование планировщика:**
```bash
# Показать все запланированные задачи
php artisan schedule:list

# Запустить планировщик один раз
php artisan schedule:run

# Запустить конкретную команду
php artisan currencies:update
```

---

### Способ 2: Прямой cron/Task Scheduler

#### Для Windows (Task Scheduler):
1. Откройте "Планировщик задач"
2. Создайте задачу:
   - **Триггер**: Ежедневно в 00:00
   - **Программа**: `C:\OSPanel\modules\php\PHP_8.2\php.exe`
   - **Аргументы**: `artisan currencies:update`
   - **Рабочая папка**: `C:\OSPanel\domains\laravel`

#### Для Linux (crontab):
```bash
# Каждый день в 00:00
0 0 * * * cd /path/to/laravel && php artisan currencies:update >> /var/log/laravel_currencies.log 2>&1
```

---

### Способ 3: Веб-запрос по расписанию

#### Создание публичного endpoint:

Добавьте в `routes/web.php`:
```php
Route::get('/cron/update-currencies/{secret}', function($secret) {
    if ($secret !== env('CRON_SECRET', 'your-secret-key')) {
        abort(403);
    }
    
    try {
        $service = new \App\Services\PocketOptionParserService();
        $count = $service->updateCurrenciesInDatabase();
        
        return response()->json([
            'success' => true,
            'message' => "Updated {$count} currencies",
            'timestamp' => now()
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage()
        ], 500);
    }
});
```

#### Настройка external cron сервиса:
Используйте сервисы типа:
- **cron-job.org**
- **easycron.com**
- **setcronjob.com**

URL для вызова:
```
http://your-domain.com/cron/update-currencies/your-secret-key
```

---

## 📋 Дополнительные настройки

### Изменение времени запуска:

В `app/Console/Kernel.php`:
```php
// Каждый день в 02:30
->dailyAt('02:30')

// Каждый понедельник в 00:00
->weeklyOn(1, '00:00')

// Первого числа каждого месяца
->monthlyOn(1, '00:00')

// Каждые 6 часов
->everySixHours()

// В рабочие дни в 08:00
->weekdays()->dailyAt('08:00')
```

### Логирование:

Добавьте логирование в планировщик:
```php
$schedule->command('currencies:update')
         ->dailyAt('00:00')
         ->sendOutputTo(storage_path('logs/currencies_scheduler.log'))
         ->emailOutputOnFailure('admin@example.com');
```

### Уведомления об ошибках:

В `.env` файле:
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
```

---

## 🔍 Мониторинг и проверка

### Проверка статуса:
```bash
# Показать список задач
php artisan schedule:list

# Показать время следующего запуска
php artisan schedule:list --timezone=Europe/Moscow
```

### Логи для мониторинга:
- Laravel логи: `storage/logs/laravel.log`
- Scheduler логи: `storage/logs/currencies_scheduler.log`
- System cron логи: `/var/log/cron` (Linux)

### Тестирование:
```bash
# Запустить планировщик вручную
php artisan schedule:run

# Запустить только обновление валют
php artisan currencies:update

# Запустить с принудительным обновлением
php artisan currencies:update --force
```

---

## ⚠️ Важные моменты

1. **Часовой пояс**: Убедитесь, что в `config/app.php` установлен правильный timezone
2. **Права доступа**: Убедитесь, что планировщик имеет права на запись в логи
3. **Memory limit**: Для больших объемов данных может потребоваться увеличить memory_limit
4. **Backup**: Рекомендуется делать бэкап БД перед обновлениями

## 🎯 Рекомендуемая настройка

Для продакшена рекомендую:
1. Использовать Laravel планировщик
2. Настроить email уведомления об ошибках
3. Включить логирование
4. Настроить мониторинг логов 