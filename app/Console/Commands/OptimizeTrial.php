<?php

namespace App\Console\Commands;

use App\Services\TrialOptimizationService;
use Illuminate\Console\Command;

class OptimizeTrial extends Command
{
    protected $signature = 'trial:optimize';

    protected $description = 'Otimiza trial com nudges e liberacao estrategica de features';

    public function handle(TrialOptimizationService $trialService): int
    {
        $result = $trialService->optimize();
        $this->info('Trial optimization: '.json_encode($result));

        return self::SUCCESS;
    }
}
