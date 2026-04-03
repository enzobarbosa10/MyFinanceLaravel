<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Services\ProductInsightsService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;

class ProductInsightsController extends Controller
{
    public function __construct(private readonly ProductInsightsService $insightsService) {}

    public function index(): JsonResponse
    {
        return ApiResponse::success($this->insightsService->insights());
    }
}
