<?php

namespace App\Console\Commands;

use App\Models\Payment;
use App\Models\UserSubscription;
use Illuminate\Console\Command;

class MarkPastDueSubscriptions extends Command
{
    protected $signature = 'mark:past-due';

    protected $description = 'Marca assinaturas com pagamentos pendentes/falhos como inadimplentes';

    public function handle(): int
    {
        $subscriptionIds = Payment::query()
            ->whereIn('status', [Payment::STATUS_PENDING, Payment::STATUS_FAILED])
            ->pluck('user_subscription_id');

        $count = UserSubscription::query()
            ->whereIn('id', $subscriptionIds)
            ->whereIn('status', [UserSubscription::STATUS_PENDING, UserSubscription::STATUS_ACTIVE])
            ->update(['status' => UserSubscription::STATUS_PAST_DUE]);

        $this->info("Assinaturas marcadas como past_due: {$count}");

        return self::SUCCESS;
    }
}
