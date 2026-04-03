<?php

namespace App\Services\Payments;

use App\Contracts\PaymentGatewayInterface;
use InvalidArgumentException;

class PaymentGatewayResolver
{
    public function resolve(?string $name): ?PaymentGatewayInterface
    {
        if (! $name) {
            return null;
        }

        return match ($name) {
            'stripe' => app(\App\Services\Gateways\StripeGateway::class),
            'pagseguro' => app(\App\Services\Gateways\PagSeguroGateway::class),
            default => throw new InvalidArgumentException('Gateway de pagamento inválido.'),
        };
    }
}
