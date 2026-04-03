<?php

namespace App\Listeners;

use App\Events\PaymentConfirmed;
use App\Events\SubscriptionCreated;
use App\Models\SubscriptionLog;
use Illuminate\Contracts\Queue\ShouldQueue;

class QueueSubscriptionAudit implements ShouldQueue
{
    public string $queue = 'payments';

    public function handle(object $event): void
    {
        if ($event instanceof SubscriptionCreated) {
            $subscription = $event->subscription;
            SubscriptionLog::create([
                'subscription_id' => $subscription->id,
                'user_id' => $subscription->user_id,
                'action' => 'event.subscription_created',
                'gateway' => $subscription->gateway,
                'payload' => ['status' => $subscription->status],
                'logged_at' => now(),
            ]);
            return;
        }

        if ($event instanceof PaymentConfirmed) {
            $payment = $event->payment;
            SubscriptionLog::create([
                'subscription_id' => $payment->user_subscription_id,
                'user_id' => $payment->user_id,
                'action' => 'event.payment_confirmed',
                'gateway' => $payment->gateway,
                'payload' => [
                    'payment_id' => $payment->id,
                    'gateway_payment_id' => $payment->gateway_payment_id,
                ],
                'logged_at' => now(),
            ]);
        }
    }
}
