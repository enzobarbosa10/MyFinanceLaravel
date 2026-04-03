<?php

namespace App\Listeners;

use App\Events\PaymentConfirmed;
use App\Events\SubscriptionCreated;
use App\Notifications\SubscriptionLifecycleNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

class QueueSubscriptionNotifications implements ShouldQueue
{
    public string $queue = 'payments';

    public function handle(object $event): void
    {
        if ($event instanceof SubscriptionCreated) {
            $subscription = $event->subscription->loadMissing('user', 'plan');
            $subscription->user?->notify(new SubscriptionLifecycleNotification(
                'Assinatura criada',
                "Sua assinatura do plano {$subscription->plan?->name} foi criada com status {$subscription->status}."
            ));
            return;
        }

        if ($event instanceof PaymentConfirmed) {
            $payment = $event->payment->loadMissing('user', 'subscription.plan');
            $payment->user?->notify(new SubscriptionLifecycleNotification(
                'Pagamento confirmado',
                "Recebemos seu pagamento do plano {$payment->subscription?->plan?->name}."
            ));
        }
    }
}
