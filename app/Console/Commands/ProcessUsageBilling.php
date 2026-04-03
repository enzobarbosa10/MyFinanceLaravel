<?php

namespace App\Console\Commands;

use App\Services\UsageAggregatorService;
use Illuminate\Console\Command;

class ProcessUsageBilling extends Command
{
    protected $signature = 'process:usage-billing {period?}';

    protected $description = 'Fecha o ciclo de billing por uso e agrega consumo por período';

    public function handle(UsageAggregatorService $aggregator): int
    {
        $period = $this->argument('period');
        $count = $aggregator->aggregate($period ?: null);

        $this->info("Agregações de uso processadas: {$count}");

        return self::SUCCESS;
    }
}
