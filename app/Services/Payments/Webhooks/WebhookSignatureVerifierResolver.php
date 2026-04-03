<?php

namespace App\Services\Payments\Webhooks;

class WebhookSignatureVerifierResolver
{
    /**
     * @var array<int, WebhookSignatureVerifierInterface>
     */
    private array $verifiers;

    public function __construct(StripeWebhookSignatureVerifier $stripeVerifier)
    {
        $this->verifiers = [$stripeVerifier];
    }

    public function verify(string $gateway, string $payload, array $headers): void
    {
        foreach ($this->verifiers as $verifier) {
            if ($verifier->supports($gateway)) {
                $verifier->verify($payload, $headers);
                return;
            }
        }
    }
}
