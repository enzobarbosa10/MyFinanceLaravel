<?php

namespace App\Contracts;

use App\Models\Plan;
use App\Models\User;

interface PaymentGatewayInterface
{
    /**
     * Nome do gateway (ex: 'stripe', 'pagseguro').
     */
    public function name(): string;

    /**
     * Cobra o usuário pelo plano.
     *
     * @return array{id: string, status: string, ...}
     */
    public function charge(User $user, Plan $plan): array;

    /**
     * Cria assinatura recorrente no gateway.
     *
     * @return array{id: string, status: string, ...}
     */
    public function createSubscription(User $user, Plan $plan): array;

    /**
     * Cancela assinatura no gateway.
     */
    public function cancelSubscription(string $gatewaySubscriptionId): bool;

    /**
     * Processa webhook do gateway e retorna dados normalizados.
     *
     * @return array{
     *   event: string,
     *   event_id: ?string,
     *   event_type: ?string,
     *   subscription_id: ?string,
     *   payment_id: ?string,
     *   status: string
     * }
     */
    public function handleWebhook(array $payload): array;
}
