<?php

use App\Http\Controllers\Api\AiAssistantController;
use App\Http\Controllers\Api\DashboardApiController;
use App\Http\Controllers\Api\PlanController;
use Illuminate\Support\Facades\Route;

// Público — lista de planos
Route::get('/plans', [PlanController::class, 'index'])->name('api.plans.index');

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
});
