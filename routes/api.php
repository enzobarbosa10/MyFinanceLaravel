<?php

use App\Http\Controllers\Api\AiAssistantController;
use App\Http\Controllers\Api\Admin\BillingMetricsController;
use App\Http\Controllers\Api\Admin\ProductInsightsController;
use App\Http\Controllers\Api\DashboardApiController;
use App\Http\Controllers\Api\OpenFinanceController;
use App\Http\Controllers\Api\PaymentWebhookController;
use App\Http\Controllers\Api\PlanController;
use App\Http\Controllers\Api\PluggyWebhookController;
use Illuminate\Support\Facades\Route;

$registerApiV1 = function (): void {
    Route::get('/plans', [PlanController::class, 'index'])->name('api.plans.index');

    Route::post('/webhooks/pluggy', [PluggyWebhookController::class, 'handle'])
        ->middleware(['pluggy.webhook.signature', 'throttle:60,1'])
        ->name('api.webhooks.pluggy');
    Route::post('/webhooks/payments/{gateway}', [PaymentWebhookController::class, 'handle'])
        ->middleware(['payment.webhook.signature', 'throttle:payment-webhooks'])
        ->name('api.webhooks.payments');

    Route::middleware(['auth:sanctum'])->group(function () {
        Route::prefix('plans')->name('api.plans.')->group(function () {
            Route::get('/current', [PlanController::class, 'current'])->name('current');
            Route::post('/subscribe', [PlanController::class, 'subscribe'])
                ->middleware(['idempotency', 'throttle:subscription-mutations'])
                ->name('subscribe');
            Route::put('/change', [PlanController::class, 'changePlan'])
                ->middleware(['idempotency', 'throttle:subscription-mutations'])
                ->name('change');
            Route::post('/cancel', [PlanController::class, 'cancel'])
                ->middleware(['idempotency', 'throttle:subscription-mutations'])
                ->name('cancel');
        });

        Route::post('/assistant/ask', [AiAssistantController::class, 'ask'])
            ->middleware(['intelligent.paywall:ai_assistant', 'plan.feature:ai_assistant', 'usage.limit:ai_assistant'])
            ->name('api.assistant.ask');

        Route::get('/dashboard', [DashboardApiController::class, 'index'])
            ->name('api.dashboard');
        Route::get('/dashboard/saldo', [DashboardApiController::class, 'saldo'])
            ->name('api.dashboard.saldo');
        Route::get('/dashboard/gastos-mes', [DashboardApiController::class, 'gastosMes'])
            ->name('api.dashboard.gastos-mes');
        Route::get('/dashboard/gastos-categoria', [DashboardApiController::class, 'gastosCategoria'])
            ->name('api.dashboard.gastos-categoria');
        Route::get('/dashboard/projecao', [DashboardApiController::class, 'projecao'])
            ->name('api.dashboard.projecao');
        Route::get('/dashboard/insights', [DashboardApiController::class, 'insights'])
            ->name('api.dashboard.insights');

        Route::prefix('open-finance')->name('api.open-finance.')->group(function () {
            Route::post('/connect-token', [OpenFinanceController::class, 'connectToken'])->name('connect-token');
            Route::get('/connectors', [OpenFinanceController::class, 'connectors'])->name('connectors');
            Route::post('/on-connect', [OpenFinanceController::class, 'onConnect'])->name('on-connect');
            Route::post('/sync', [OpenFinanceController::class, 'sync'])->name('sync');
            Route::post('/disconnect', [OpenFinanceController::class, 'disconnect'])->name('disconnect');
        });

        Route::prefix('admin')->middleware('admin.access')->group(function () {
            Route::get('/billing/metrics', [BillingMetricsController::class, 'index'])
                ->name('api.admin.billing.metrics');
            Route::get('/product/insights', [ProductInsightsController::class, 'index'])
                ->name('api.admin.product.insights');
        });
    });
};

$registerApiV1();

Route::prefix('v1')->group($registerApiV1);
