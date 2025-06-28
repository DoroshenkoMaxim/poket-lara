<?php

namespace App\Console\Commands;

use App\Services\PocketOptionParserService;
use Illuminate\Console\Command;

class UpdateCurrenciesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'currencies:update {--force : Принудительное обновление даже при ошибках}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Обновление валют из PocketOption';

    protected $parserService;

    public function __construct(PocketOptionParserService $parserService)
    {
        parent::__construct();
        $this->parserService = $parserService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Начинаем обновление валют из PocketOption...');
        
        try {
            $updatedCount = $this->parserService->updateCurrenciesInDatabase();
            
            $this->info("✅ Успешно обновлено {$updatedCount} валют");
            
            // Показываем статистику
            $stats = $this->parserService->getCurrencyStats();
            $this->table(
                ['Метрика', 'Значение'],
                [
                    ['Всего валют', $stats['total']],
                    ['Активных валют', $stats['active']],
                    ['OTC валют', $stats['otc']],
                    ['Последнее обновление', $stats['last_update']?->format('Y-m-d H:i:s') ?? 'Никогда']
                ]
            );
            
            return Command::SUCCESS;
            
        } catch (\Exception $e) {
            $this->error('❌ Ошибка при обновлении валют: ' . $e->getMessage());
            
            if ($this->option('force')) {
                $this->warn('Продолжаем выполнение с флагом --force');
                return Command::SUCCESS;
            }
            
            return Command::FAILURE;
        }
    }
} 