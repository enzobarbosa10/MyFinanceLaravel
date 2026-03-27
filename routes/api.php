<?php

use App\Http\Controllers\Api\AiAssistantController;
use App\Http\Controllers\Api\DashboardApiController;
use App\Http\Controllers\Api\OpenFinanceController;
use App\Http\Controllers\Api\PlanController;
use App\Http\Controllers\Api\PluggyWebhookController;
use Illuminate\Support\Facades\Route;

// Público — lista de planos
Route::get('/plans', [PlanController::class, 'index'])->name('api.plans.index');

// Webhook Pluggy (chamado pela Pluggy, sem auth)
Route::post('/webhooks/pluggy', [PluggyWebhookController::class, 'handle'])->name('api.webhooks.pluggy');

Route::middleware(['auth:sanctum'])->group(function () {

    // ── Planos / Assinatura ──────────────────────────────────
    Route::prefix('plans')->name('api.plans.')->group(function () {
        Route::get('/current', [PlanController::class, 'current'])->name('current');
        Route::post('/subscribe', [PlanController::class, 'subscribe'])->name('subscribe');
        Route::put('/change', [PlanController::class, 'changePlan'])->name('change');
        Route::post('/cancel', [PlanController::class, 'cancel'])->name('cancel');
    });

    // Assistente financeiro IA (requer feature ai_assistant)
    Route::post('/assistant/ask', [AiAssistantController::class, 'ask'])
        ->middleware('plan.feature:ai_assistant')
        ->name('api.assistant.ask');

    // Dashboard — resumo completo
    Route::get('/dashboard', [DashboardApiController::class, 'index'])
        ->name('api.dashboard');

    // Dashboard — endpoints individuais
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

    // ── Open Finance ─────────────────────────────────────────
    Route::prefix('open-finance')->name('api.open-finance.')->group(function () {
        Route::post('/connect-token', [OpenFinanceController::class, 'connectToken'])->name('connect-token');
        Route::get('/connectors', [OpenFinanceController::class, 'connectors'])->name('connectors');
        Route::post('/on-connect', [OpenFinanceController::class, 'onConnect'])->name('on-connect');
        Route::post('/sync', [OpenFinanceController::class, 'sync'])->name('sync');
        Route::post('/disconnect', [OpenFinanceController::class, 'disconnect'])->name('disconnect');
    });
});
