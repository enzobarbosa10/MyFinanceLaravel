<?php

namespace App\Services\Concerns;

use App\Exceptions\PaymentGatewayUnavailableException;
use Illuminate\Support\Facades\Cache;
use Throwable;

trait HandlesCircuitBreaker
{
    private function runGatewayCall(callable $callback, string $gateway, string $operation): array
    {
        $this->ensureCircuitIsClosed($gateway);

        $attempts = (int) config('subscriptions.gateway.retry.attempts', 3);
        $baseDelayMs = (int) config('subscriptions.gateway.retry.base_delay_ms', 200);
        $timeoutSeconds = (int) config('subscriptions.gateway.timeout_seconds', 10);
        $lastException = null;

        for ($attempt = 1; $attempt <= $attempts; $attempt++) {
            $startedAt = microtime(true);

            try {
                $result = $callback();
                $elapsedSeconds = microtime(true) - $startedAt;

                if ($elapsedSeconds > $timeoutSeconds) {
                    throw new PaymentGatewayUnavailableException("Timeout ao executar {$operation} no gateway {$gateway}.");
                }

                $this->resetCircuit($gateway);

                return $result;
            } catch (Throwable $e) {
                $lastException = $e;
                $this->registerCircuitFailure($gateway);

                if ($attempt === $attempts) {
                    break;
                }

                $delayMs = $baseDelayMs * (2 ** ($attempt - 1));
                usleep($delayMs * 1000);
            }
        }

        throw new PaymentGatewayUnavailableException(
            "Falha ao executar {$operation} no gateway {$gateway} após {$attempts} tentativas.",
            0,
            $lastException,
        );
    }

    private function ensureCircuitIsClosed(string $gateway): void
    {
        $openUntil = Cache::get($this->circuitOpenUntilCacheKey($gateway));

        if (! $openUntil) {
            return;
        }

        if (now()->lt($openUntil)) {
            $this->alertService->alertCircuitBreakerOpen($gateway, ['open_until' => $openUntil]);
            throw new PaymentGatewayUnavailableException("Circuit breaker aberto para gateway {$gateway}.");
        }

        $this->resetCircuit($gateway);
    }

    private function registerCircuitFailure(string $gateway): void
    {
        $failures = Cache::increment($this->circuitFailureCountCacheKey($gateway));
        $threshold = (int) config('subscriptions.gateway.circuit_breaker.failure_threshold', 5);

        if ($failures < $threshold) {
            return;
        }

        $cooldown = (int) config('subscriptions.gateway.circuit_breaker.cooldown_seconds', 60);
        Cache::put($this->circuitOpenUntilCacheKey($gateway), now()->addSeconds($cooldown), now()->addSeconds($cooldown));
        $this->alertService->alertCircuitBreakerOpen($gateway, ['cooldown_seconds' => $cooldown]);
    }

    private function resetCircuit(string $gateway): void
    {
        Cache::forget($this->circuitFailureCountCacheKey($gateway));
        Cache::forget($this->circuitOpenUntilCacheKey($gateway));
    }

    private function circuitFailureCountCacheKey(string $gateway): string
    {
        return 'payment:circuit:'.$gateway.':failures';
    }

    private function circuitOpenUntilCacheKey(string $gateway): string
    {
        return 'payment:circuit:'.$gateway.':open_until';
    }
}
