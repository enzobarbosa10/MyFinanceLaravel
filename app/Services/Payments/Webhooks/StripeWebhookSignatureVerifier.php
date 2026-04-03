<?php

namespace App\Services\Payments\Webhooks;

use App\Exceptions\InvalidWebhookSignatureException;
use App\Exceptions\WebhookReplayAttackException;

class StripeWebhookSignatureVerifier implements WebhookSignatureVerifierInterface
{
    public function supports(string $gateway): bool
    {
        return $gateway === 'stripe';
    }

    public function verify(string $payload, array $headers): void
    {
        $signatureHeader = $this->extractHeader($headers, 'Stripe-Signature');
        if (! $signatureHeader) {
            throw new InvalidWebhookSignatureException('Header Stripe-Signature ausente.');
        }

        $secret = (string) config('services.stripe.webhook_secret');
        if ($secret === '') {
            throw new InvalidWebhookSignatureException('STRIPE_WEBHOOK_SECRET não configurado.');
        }

        $parts = $this->parseHeader($signatureHeader);
        $timestamp = isset($parts['t']) ? (int) $parts['t'] : 0;
        $expected = hash_hmac('sha256', $timestamp.'.'.$payload, $secret);

        if (! isset($parts['v1']) || ! hash_equals((string) $parts['v1'], $expected)) {
            throw new InvalidWebhookSignatureException('Assinatura de webhook inválida.');
        }

        $tolerance = (int) config('subscriptions.webhook.replay_tolerance_seconds', 300);
        if (abs(now()->timestamp - $timestamp) > $tolerance) {
            throw new WebhookReplayAttackException('Webhook com timestamp fora da janela de tolerância.');
        }
    }

    private function extractHeader(array $headers, string $name): ?string
    {
        $normalized = array_change_key_case($headers, CASE_LOWER);
        $value = $normalized[strtolower($name)] ?? null;

        if (is_array($value)) {
            return isset($value[0]) ? (string) $value[0] : null;
        }

        return $value !== null ? (string) $value : null;
    }

    /**
     * @return array<string, string>
     */
    private function parseHeader(string $signatureHeader): array
    {
        $parsed = [];

        foreach (explode(',', $signatureHeader) as $piece) {
            [$key, $value] = array_pad(explode('=', trim($piece), 2), 2, null);
            if ($key && $value) {
                $parsed[$key] = $value;
            }
        }

        return $parsed;
    }
}
