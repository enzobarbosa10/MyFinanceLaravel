<?php

namespace App\Services\Payments;

use Illuminate\Support\Facades\Cache;

class SubscriptionTelemetryService
{
    public function increment(string $metric): void
    {
        Cache::increment('metrics:subscriptions:'.$metric);
    }

    public function observeWebhookDuration(string $gateway, float $milliseconds): void
    {
        $bucket = now()->format('YmdHi');
        Cache::put("metrics:webhook:{$gateway}:duration_ms:last", $milliseconds, now()->addHours(2));
        Cache::increment("metrics:webhook:{$gateway}:count:{$bucket}");
    }
}
