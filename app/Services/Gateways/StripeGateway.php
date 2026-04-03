<?php

namespace App\Services\Gateways;

use App\Contracts\PaymentGatewayInterface;
use App\Models\Plan;
use App\Models\User;

class StripeGateway implements PaymentGatewayInterface
{
    public function name(): string
    {
        return 'stripe';
    }

    public function charge(User $user, Plan $plan): array
    {
        // TODO: Integrar com Stripe API
        // Exemplo: \Stripe\Charge::create([...])
        //
        // $stripe = new \Stripe\StripeClient(config('services.stripe.secret'));
        // $charge = $stripe->charges->create([
        //     'amount' => (int) ($plan->price * 100),
        //     'currency' => 'brl',
        //     'customer' => $user->stripe_customer_id,
        //     'description' => "Plano {$plan->name}",
        // ]);
        //
        // return [
        //     'id' => $charge->id,
        //     'status' => $charge->status === 'succeeded' ? 'paid' : 'pending',
        // ];

        return [
            'id' => 'stripe_ch_' . uniqid(),
            'status' => 'paid',
            'gateway' => 'stripe',
        ];
    }

    public function createSubscription(User $user, Plan $plan): array
    {
        // TODO: Integrar com Stripe Subscriptions API
        // $stripe = new \Stripe\StripeClient(config('services.stripe.secret'));
        // $sub = $stripe->subscriptions->create([
        //     'customer' => $user->stripe_customer_id,
        //     'items' => [['price' => $plan->stripe_price_id]],
        // ]);

        return [
            'id' => 'stripe_sub_' . uniqid(),
            'status' => 'active',
        ];
    }

    public function cancelSubscription(string $gatewaySubscriptionId): bool
    {
        // TODO: $stripe->subscriptions->cancel($gatewaySubscriptionId);
        return true;
    }

    public function handleWebhook(array $payload): array
    {
        // TODO: Processar eventos do Stripe webhook
        // Eventos comuns: invoice.paid, invoice.payment_failed, customer.subscription.deleted

        $type = $payload['type'] ?? 'unknown';

        return match ($type) {
            'invoice.paid' => [
                'event' => 'payment_success',
                'event_id' => $payload['id'] ?? null,
                'event_type' => $type,
                'subscription_id' => $payload['data']['object']['subscription'] ?? null,
                'payment_id' => $payload['data']['object']['payment_intent'] ?? null,
                'status' => 'paid',
            ],
            'invoice.payment_failed' => [
                'event' => 'payment_failed',
                'event_id' => $payload['id'] ?? null,
                'event_type' => $type,
                'subscription_id' => $payload['data']['object']['subscription'] ?? null,
                'payment_id' => null,
                'status' => 'failed',
            ],
            'customer.subscription.deleted' => [
                'event' => 'subscription_canceled',
                'event_id' => $payload['id'] ?? null,
                'event_type' => $type,
                'subscription_id' => $payload['data']['object']['id'] ?? null,
                'payment_id' => null,
                'status' => 'canceled',
            ],
            default => [
                'event' => $type,
                'event_id' => $payload['id'] ?? null,
                'event_type' => $type,
                'subscription_id' => null,
                'payment_id' => null,
                'status' => 'unknown',
            ],
        };
    }
}
