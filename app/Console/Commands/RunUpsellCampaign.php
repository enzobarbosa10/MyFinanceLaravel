<?php

namespace App\Console\Commands;

use App\Services\UpsellRecommendationService;
use Illuminate\Console\Command;

class RunUpsellCampaign extends Command
{
    protected $signature = 'upsell:run';

    protected $description = 'Executa campanha automatica de upsell com base em uso e limite';

    public function handle(UpsellRecommendationService $upsellService): int
    {
        $result = $upsellService->runAutomaticUpsellCampaign();
        $this->info('Upsell executado: '.json_encode($result));

        return self::SUCCESS;
    }
}
