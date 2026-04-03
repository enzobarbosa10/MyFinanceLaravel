<?php

namespace App\Console\Commands;

use App\Services\ChurnRecoveryService;
use Illuminate\Console\Command;

class RecoverChurn extends Command
{
    protected $signature = 'churn:recover';

    protected $description = 'Executa fluxo automatico de recuperacao de churn';

    public function handle(ChurnRecoveryService $recoveryService): int
    {
        $result = $recoveryService->recoverPastDueSubscriptions();
        $this->info('Churn recovery: '.json_encode($result));

        return self::SUCCESS;
    }
}
