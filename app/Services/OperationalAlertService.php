<?php

namespace App\Services;

use App\Notifications\OperationalAlertNotifiable;
use App\Notifications\OperationalAlertNotification;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class OperationalAlertService
{
    public function alertPaymentFailure(array $context): void
    {
        $this->send('Pagamento com falha', 'Uma falha de pagamento foi detectada.', $context);
    }

    public function alertCircuitBreakerOpen(string $gateway, array $context = []): void
    {
        $this->send('Circuit breaker aberto', 'O gateway entrou em proteção de circuit breaker.', array_merge(['gateway' => $gateway], $context));
    }

    public function alertWebhookFailureRate(string $gateway, float $failureRate, array $context = []): void
    {
        $threshold = (float) config('services.operations.webhook_failure_threshold', 5.0);
        if ($failureRate < $threshold) {
            return;
        }

        $this->send('Taxa de falha de webhook elevada', 'A taxa de falha de webhooks ultrapassou o limite.', array_merge([
            'gateway' => $gateway,
            'failure_rate' => $failureRate,
        ], $context));
    }

    public function incrementWebhookResult(string $gateway, bool $success): void
    {
        $bucket = now()->format('YmdHi');
        Cache::increment("ops:webhook:{$gateway}:total:{$bucket}");
        Cache::increment("ops:webhook:{$gateway}:".($success ? 'success' : 'failure').":{$bucket}");
    }

    public function failureRate(string $gateway): float
    {
        $bucket = now()->format('YmdHi');
        $total = (int) Cache::get("ops:webhook:{$gateway}:total:{$bucket}", 0);
        $failed = (int) Cache::get("ops:webhook:{$gateway}:failure:{$bucket}", 0);

        if ($total === 0) {
            return 0.0;
        }

        return round(($failed / $total) * 100, 2);
    }

    private function send(string $title, string $message, array $context): void
    {
        Notification::route('mail', (new OperationalAlertNotifiable())->routeNotificationForMail())
            ->notify(new OperationalAlertNotification($title, $message, $context));

        $webhookUrl = config('services.operations.slack_webhook_url');
        if (! $webhookUrl) {
            Log::warning('operations.alert', [
                'title' => $title,
                'message' => $message,
                'context' => $context,
            ]);
            return;
        }

        Http::timeout(5)->post($webhookUrl, [
            'text' => $title,
            'attachments' => [[
                'text' => $message,
                'fields' => collect($context)->map(fn ($value, $key) => [
                    'title' => (string) $key,
                    'value' => is_scalar($value) ? (string) $value : json_encode($value),
                    'short' => true,
                ])->values()->all(),
            ]],
        ]);
    }
}
