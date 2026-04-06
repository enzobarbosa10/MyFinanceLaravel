<?php

namespace App\Services\Gateways;

use App\Contracts\PaymentGatewayInterface;
use App\Models\Plan;
use App\Models\User;

class PagSeguroGateway implements PaymentGatewayInterface
{
    public function name(): string
    {
        return 'pagseguro';
    }

    public function charge(User $user, Plan $plan): array
    {
        // [STUB] Integrar com PagSeguro API
        //
        // $response = Http::withHeaders([
        //     'Authorization' => 'Bearer ' . config('services.pagseguro.token'),
        //     'Content-Type' => 'application/json',
        // ])->post('https://api.pagseguro.com/orders', [
        //     'reference_id' => "plan_{$plan->id}_user_{$user->id}",
        //     'items' => [[
        //         'name' => $plan->name,
        //         'quantity' => 1,
        //         'unit_amount' => (int) ($plan->price * 100),
        //     ]],
        //     'charges' => [[
        //         'amount' => ['value' => (int) ($plan->price * 100), 'currency' => 'BRL'],
        //     ]],
        // ]);

        return [
            'id' => 'pagseguro_' . uniqid(),
            'status' => 'paid',
            'gateway' => 'pagseguro',
        ];
    }

    public function createSubscription(User $user, Plan $plan): array
    {
        // [STUB] Integrar com PagSeguro Assinaturas API
        // POST https://api.pagseguro.com/pre-approvals

        return [
            'id' => 'pagseguro_sub_' . uniqid(),
            'status' => 'active',
        ];
    }

    public function cancelSubscription(string $gatewaySubscriptionId): bool
    {
        // [STUB] PUT /pre-approvals/{id}/cancel
        return true;
    }

    public function handleWebhook(array $payload): array
    {
        // [STUB] Processar notificações do PagSeguro
        // Tipos: transaction, preApproval

        $type = $payload['notificationType'] ?? 'unknown';

        return match ($type) {
            'transaction' => [
                'event' => 'payment_update',
                'event_id' => $payload['notificationCode'] ?? null,
                'event_type' => $type,
                'subscription_id' => $payload['preApprovalCode'] ?? null,
                'payment_id' => $payload['notificationCode'] ?? null,
                'status' => $this->mapPagSeguroStatus($payload['status'] ?? 0),
            ],
            default => [
                'event' => $type,
                'event_id' => $payload['notificationCode'] ?? null,
                'event_type' => $type,
                'subscription_id' => null,
                'payment_id' => null,
                'status' => 'unknown',
            ],
        };
    }

    private function mapPagSeguroStatus(int|string $status): string
    {
        return match ((int) $status) {
            3 => 'paid',       // Paga
            6 => 'refunded',   // Devolvida
            7 => 'canceled',   // Cancelada
            default => 'pending',
        };
    }
}
