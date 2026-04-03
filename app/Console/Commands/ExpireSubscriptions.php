<?php

namespace App\Console\Commands;

use App\Models\UserSubscription;
use Illuminate\Console\Command;

class ExpireSubscriptions extends Command
{
    protected $signature = 'expire:subscriptions';

    protected $description = 'Expira assinaturas cujo vencimento já passou';

    public function handle(): int
    {
        $count = UserSubscription::query()
            ->whereIn('status', [UserSubscription::STATUS_ACTIVE, UserSubscription::STATUS_TRIALING, UserSubscription::STATUS_PAST_DUE])
            ->whereNotNull('expires_at')
            ->where('expires_at', '<=', now())
            ->update(['status' => UserSubscription::STATUS_EXPIRED]);

        $this->info("Assinaturas expiradas: {$count}");

        return self::SUCCESS;
    }
}
