<?php

namespace App\Services;

use App\Models\FeatureUsage;
use App\Models\UsageRecord;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class UsageTrackingService
{
    public function record(User $user, string $feature, int $quantity = 1, array $metadata = []): void
    {
        DB::transaction(function () use ($user, $feature, $quantity, $metadata) {
            $period = now()->format('Y-m');
            $plan = $user->currentPlan() ?? \App\Models\Plan::free();

            UsageRecord::create([
                'user_id' => $user->id,
                'plan_id' => $plan?->id,
                'feature' => $feature,
                'quantity' => $quantity,
                'billing_period' => $period,
                'metadata' => $metadata,
            ]);

            $usage = FeatureUsage::firstOrCreate([
                'user_id' => $user->id,
                'feature' => $feature,
                'period' => $period,
            ], [
                'plan_id' => $plan?->id,
                'usage_count' => 0,
                'metadata' => [],
            ]);

            $usage->increment('usage_count', $quantity);
        });
    }

    public function currentUsage(User $user, string $feature, ?string $period = null): int
    {
        return (int) FeatureUsage::query()
            ->where('user_id', $user->id)
            ->where('feature', $feature)
            ->where('period', $period ?? now()->format('Y-m'))
            ->value('usage_count');
    }
}
