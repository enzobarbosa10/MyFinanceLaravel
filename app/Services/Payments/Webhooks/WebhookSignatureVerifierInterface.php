<?php

namespace App\Services\Payments\Webhooks;

interface WebhookSignatureVerifierInterface
{
    public function supports(string $gateway): bool;

    /**
     * @param array<string, string|array<int, string>|null> $headers
     */
    public function verify(string $payload, array $headers): void;
}
