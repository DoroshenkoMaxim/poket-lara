<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Обновление валют каждый день в 00:00
        $schedule->command('currencies:update')
                 ->dailyAt('00:00')
                 ->withoutOverlapping()
                 ->runInBackground()
                 ->emailOutputOnFailure('admin@example.com'); // замените на ваш email
        
        // Альтернативные варианты расписания:
        // ->daily() - каждый день в 00:00 (аналогично dailyAt('00:00'))
        // ->dailyAt('02:30') - каждый день в 02:30
        // ->weeklyOn(1, '00:00') - каждый понедельник в 00:00
        // ->monthlyOn(1, '00:00') - первого числа каждого месяца в 00:00
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
