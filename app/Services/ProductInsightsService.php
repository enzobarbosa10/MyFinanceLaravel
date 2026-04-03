<?php

namespace App\Services;

use App\Models\FeatureUsage;
use App\Models\UserSubscription;
use Illuminate\Support\Facades\Cache;

class ProductInsightsService
{
    public function __construct(private readonly UserSegmentationService $segmentationService) {}

    public function insights(): array
    {
        return Cache::remember('product:insights:v1', now()->addMinutes(10), function () {
            return [
                'segments' => $this->segmentationService->countsBySegment(),
                'avg_usage_by_plan' => $this->avgUsageByPlan(),
                'upgrade_rate' => $this->upgradeRate(),
                'churn_rate_by_plan' => $this->churnRateByPlan(),
                'generated_at' => now()->toISOString(),
            ];
        });
    }

    private function avgUsageByPlan(): array
    {
        return FeatureUsage::query()
            ->join('plans', 'plans.id', '=', 'feature_usages.plan_id')
            ->groupBy('plans.slug')
            ->select('plans.slug')
            ->selectRaw('AVG(feature_usages.usage_count) as avg_usage')
            ->get()
            ->map(fn ($row) => [
                'plan' => $row->slug,
                'avg_usage' => round((float) $row->avg_usage, 2),
            ])
            ->all();
    }

    private function upgradeRate(): float
    {
        $totalUsers = max(UserSubscription::query()->distinct('user_id')->count('user_id'), 1);
        $upgradedUsers = UserSubscription::query()
            ->whereIn('status', [UserSubscription::STATUS_ACTIVE, UserSubscription::STATUS_PAST_DUE])
            ->whereHas('plan', fn ($q) => $q->whereIn('slug', ['pro', 'premium']))
            ->distinct('user_id')
            ->count('user_id');

        return round(($upgradedUsers / $totalUsers) * 100, 2);
    }

    private function churnRateByPlan(): array
    {
        return UserSubscription::query()
            ->join('plans', 'plans.id', '=', 'user_subscriptions.plan_id')
            ->groupBy('plans.slug')
            ->select('plans.slug')
            ->selectRaw("SUM(CASE WHEN user_subscriptions.status IN ('canceled','expired') THEN 1 ELSE 0 END) as churned")
            ->selectRaw('COUNT(user_subscriptions.id) as total')
            ->get()
            ->map(fn ($row) => [
                'plan' => $row->slug,
                'churn_rate' => $row->total > 0 ? round(($row->churned / $row->total) * 100, 2) : 0.0,
            ])
            ->all();
    }
}
