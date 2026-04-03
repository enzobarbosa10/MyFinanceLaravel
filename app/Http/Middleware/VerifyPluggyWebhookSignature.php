<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class VerifyPluggyWebhookSignature
{
    public function handle(Request $request, Closure $next)
    {
        $secret = config('services.pluggy.webhook_secret');

        // If no secret is configured, skip validation (dev only)
        if (!$secret) {
            if (app()->environment('production')) {
                Log::critical('Pluggy webhook_secret is not configured in production!');
                abort(500, 'Webhook secret not configured.');
            }

            return $next($request);
        }

        $signature = $request->header('X-Pluggy-Signature');

        if (!$signature) {
            Log::warning('Pluggy webhook: missing signature header', [
                'ip' => $request->ip(),
            ]);

            return response()->json(['error' => 'Missing signature.'], 401);
        }

        $payload   = $request->getContent();
        $expected  = hash_hmac('sha256', $payload, $secret);

        if (!hash_equals($expected, $signature)) {
            Log::warning('Pluggy webhook: invalid signature', [
                'ip' => $request->ip(),
            ]);

            return response()->json(['error' => 'Invalid signature.'], 401);
        }

        return $next($request);
    }
}
