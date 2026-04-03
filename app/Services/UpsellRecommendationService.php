<?php

namespace App\Services;

use App\Models\FeatureUsage;
use App\Models\Plan;
use App\Models\User;
use App\Models\UserSubscription;
use App\Notifications\UpsellSuggestionNotification;
use Illuminate\Support\Collection;

class UpsellRecommendationService
{
    public function recommendForUser(User $user, string $feature, int $currentUsage, ?int $limit): array
    {
        $currentPlan = $user->currentPlan() ?? Plan::free();
        $recommended = $this->nextPlanSlug($currentPlan?->slug ?? 'free');

        $ratio = $limit && $limit > 0 ? round(($currentUsage / $limit) * 100, 1) : null;

        return [
            'feature' => $feature,
            'current_plan' => $currentPlan?->slug,
            'recommended_plan' => $recommended,
            'current_usage' => $currentUsage,
            'limit' => $limit,
            'usage_percent' => $ratio,
            'message' => $ratio
                ? "Voce ja usou {$ratio}% do limite de {$feature}."
                : "Seu uso de {$feature} indica potencial para upgrade.",
        ];
    }

    public function usersNearLimit(float $threshold = 0.8): Collection
    {
        $period = now()->format('Y-m');
        $users = User::with(['currentOpenSubscription.plan.features'])->get();

        return $users->filter(function (User $user) use ($threshold, $period) {
            $plan = $user->currentPlan();
            if (! $plan) {
                return false;
            }

            foreach ($plan->features as $feature) {
                if ($feature->limit_value === null || $feature->limit_value <= 0) {
                    continue;
                }

                $usage = (int) FeatureUsage::query()
                    ->where('user_id', $user->id)
                    ->where('feature', $feature->feature)
                    ->where('period', $period)
                    ->value('usage_count');

                if ($usage >= ($feature->limit_value * $threshold)) {
                    return true;
                }
            }

            return false;
        });
    }

    public function usersWithHighUsage(int $threshold = 200): Collection
    {
        $period = now()->format('Y-m');

        return User::query()
            ->whereIn('id', FeatureUsage::query()
                ->where('period', $period)
                ->groupBy('user_id')
                ->havingRaw('SUM(usage_count) >= ?', [$threshold])
                ->pluck('user_id'))
            ->get();
    }

    public function runAutomaticUpsellCampaign(): array
    {
        $nearLimit = $this->usersNearLimit();
        $powerUse = $this->usersWithHighUsage();

        $targets = $nearLimit->merge($powerUse)->unique('id');
        $sent = 0;

        foreach ($targets as $user) {
            $plan = $user->currentPlan() ?? Plan::free();
            if (! $plan) {
                continue;
            }

            $next = $this->nextPlanSlug($plan->slug);
            if (! $next) {
                continue;
            }

            (new UpsellSuggestionNotification(
                $plan->slug,
                $next,
                'Uso proximo ao limite ou elevado',
            ))->sendTo($user->id);
            $sent++;
        }

        return [
            'targets' => $targets->count(),
            'sent' => $sent,
        ];
    }

    private function nextPlanSlug(string $currentPlan): ?string
    {
        return match ($currentPlan) {
            'free' => 'pro',
            'pro' => 'premium',
            default => null,
        };
    }
}
