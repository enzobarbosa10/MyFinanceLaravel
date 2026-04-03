<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Services\BillingMetricsService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;

class BillingMetricsController extends Controller
{
    public function __construct(private readonly BillingMetricsService $metricsService) {}

    public function index(): JsonResponse
    {
        return ApiResponse::success($this->metricsService->metrics());
    }
}
