<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'subscription' => \App\Http\Middleware\CheckSubscription::class,
            'plan.feature' => \App\Http\Middleware\CheckPlanFeature::class,
            'plan.level' => \App\Http\Middleware\CheckPlanLevel::class,
            'idempotency' => \App\Http\Middleware\EnsureIdempotency::class,
            'payment.webhook.signature' => \App\Http\Middleware\VerifyPaymentWebhookSignature::class,
            'admin.access' => \App\Http\Middleware\EnsureAdminAccess::class,
            'usage.limit' => \App\Http\Middleware\CheckUsageLimit::class,
            'checkFeatureAccess' => \App\Http\Middleware\CheckPlanFeature::class,
            'checkUsageLimit' => \App\Http\Middleware\CheckUsageLimit::class,
            'intelligent.paywall' => \App\Http\Middleware\IntelligentPaywall::class,
            'pluggy.webhook.signature' => \App\Http\Middleware\VerifyPluggyWebhookSignature::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
