<?php

namespace App\Http\Middleware;

use App\Exceptions\InvalidWebhookSignatureException;
use App\Exceptions\WebhookReplayAttackException;
use App\Services\Payments\Webhooks\WebhookSignatureVerifierResolver;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class VerifyPaymentWebhookSignature
{
    public function __construct(private readonly WebhookSignatureVerifierResolver $resolver) {}

    public function handle(Request $request, Closure $next)
    {
        $gateway = (string) $request->route('gateway');

        try {
            $this->resolver->verify($gateway, $request->getContent(), $request->headers->all());
        } catch (InvalidWebhookSignatureException|WebhookReplayAttackException $e) {
            Log::warning('payment.webhook.rejected', [
                'gateway' => $gateway,
                'reason' => $e->getMessage(),
                'ip' => $request->ip(),
            ]);

            return response()->json([
                'data' => null,
                'meta' => [],
                'errors' => [[
                    'message' => 'Webhook rejeitado por falha de segurança.',
                    'details' => ['reason' => $e->getMessage()],
                ]],
            ], 401);
        }

        return $next($request);
    }
}
