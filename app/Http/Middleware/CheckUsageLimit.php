<?php

namespace App\Http\Middleware;

use App\Services\UsageTrackingService;
use App\Support\ApiResponse;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckUsageLimit
{
    public function __construct(private readonly UsageTrackingService $usageTracking) {}

    public function handle(Request $request, Closure $next, string $feature): Response
    {
        $user = $request->user();

        if (! $user) {
            return ApiResponse::error('Não autenticado.', 401);
        }

        $limit = $user->featureLimit($feature);
        if ($limit === null) {
            return $next($request);
        }

        $currentUsage = $this->usageTracking->currentUsage($user, $feature);
        if ($currentUsage >= $limit) {
            return ApiResponse::error('Limite de uso do plano atingido.', 429, [
                'feature' => $feature,
                'limit' => $limit,
                'current_usage' => $currentUsage,
            ]);
        }

        return $next($request);
    }
}
