<?php

namespace App\Http\Middleware;

use App\Services\UpsellRecommendationService;
use App\Services\UsageTrackingService;
use App\Support\ApiResponse;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IntelligentPaywall
{
    public function __construct(
        private readonly UpsellRecommendationService $upsellService,
        private readonly UsageTrackingService $usageTracking,
    ) {}

    public function handle(Request $request, Closure $next, string $feature): Response
    {
        $user = $request->user();
        if (! $user) {
            return ApiResponse::error('Nao autenticado.', 401);
        }

        $response = null;

        if (! $this->hasStrategicTrialUnlock($user->id, $feature)) {
            $limit = $user->featureLimit($feature);
            $currentUsage = $this->usageTracking->currentUsage($user, $feature);

            if (! $user->canUseFeature($feature)) {
                $recommendation = $this->upsellService->recommendForUser($user, $feature, $currentUsage, $limit);
                $response = ApiResponse::error('Recurso premium indisponivel no seu plano.', 403, [
                    'upgrade' => $recommendation,
                ]);
            }

            if (! $response && $limit !== null && $currentUsage >= $limit) {
                $recommendation = $this->upsellService->recommendForUser($user, $feature, $currentUsage, $limit);
                $response = ApiResponse::error('Limite do recurso atingido.', 429, [
                    'upgrade' => $recommendation,
                ]);
            }

            if (! $response) {
                $response = $next($request);

                if ($limit !== null && $limit > 0 && $currentUsage >= (int) floor($limit * 0.8)) {
                    $recommendation = $this->upsellService->recommendForUser($user, $feature, $currentUsage, $limit);
                    $this->appendUpgradeHint($response, $recommendation);
                }
            }
        } else {
            $response = $next($request);
        }

        return $response;
    }

    private function hasStrategicTrialUnlock(int $userId, string $feature): bool
    {
        return cache()->has("trial:unlock:{$userId}:{$feature}");
    }

    private function appendUpgradeHint(Response $response, array $recommendation): void
    {
        if ($response instanceof JsonResponse) {
            $response->headers->set('X-Upgrade-Suggestion', json_encode($recommendation));
        }
    }
}
