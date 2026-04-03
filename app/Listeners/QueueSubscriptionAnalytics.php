<?php

namespace App\Listeners;

use App\Events\PaymentConfirmed;
use App\Events\SubscriptionCreated;
use App\Services\Payments\SubscriptionTelemetryService;
use Illuminate\Contracts\Queue\ShouldQueue;

class QueueSubscriptionAnalytics implements ShouldQueue
{
    public string $queue = 'payments';

    public function __construct(private readonly SubscriptionTelemetryService $telemetry) {}

    public function handle(object $event): void
    {
        if ($event instanceof SubscriptionCreated) {
            $this->telemetry->increment('event_subscription_created_total');
            return;
        }

        if ($event instanceof PaymentConfirmed) {
            $this->telemetry->increment('event_payment_confirmed_total');
        }
    }
}
