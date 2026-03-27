<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class DashboardApiController extends Controller
{
    public function __construct(
        private DashboardService $dashboardService,
    ) {}

    /**
     * GET /api/dashboard
     * Retorna resumo completo do dashboard.
     */
    public function index(): JsonResponse
    {
        $user = Auth::user();

        return response()->json([
            'data' => $this->dashboardService->getSummary($user),
        ]);
    }

    /**
     * GET /api/dashboard/saldo
     */
    public function saldo(): JsonResponse
    {
        return response()->json([
            'data' => $this->dashboardService->getSaldoTotal(Auth::id()),
        ]);
    }

    /**
     * GET /api/dashboard/gastos-mes
     */
    public function gastosMes(): JsonResponse
    {
        $month = request()->query('month', now()->format('Y-m'));

        return response()->json([
            'data' => $this->dashboardService->getGastosMes(Auth::id(), $month),
        ]);
    }

    /**
     * GET /api/dashboard/gastos-categoria
     */
    public function gastosCategoria(): JsonResponse
    {
        $month = request()->query('month', now()->format('Y-m'));

        return response()->json([
            'data' => $this->dashboardService->getGastosPorCategoria(Auth::id(), $month),
        ]);
    }

    /**
     * GET /api/dashboard/projecao
     */
    public function projecao(): JsonResponse
    {
        return response()->json([
            'data' => $this->dashboardService->getProjecao(Auth::user()),
        ]);
    }

    /**
     * GET /api/dashboard/insights
     */
    public function insights(): JsonResponse
    {
        return response()->json([
            'data' => $this->dashboardService->getInsightsRecentes(Auth::id()),
        ]);
    }
}
