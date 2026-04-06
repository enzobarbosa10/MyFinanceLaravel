<?php

namespace App\Services;

use App\Models\FeatureUsage;
use App\Models\User;
use App\Models\UserSubscription;

class UserSegmentationService
{
    public function segmentForUser(User $user): string
    {
        $sub = $user->currentOpenSubscription;

        if (! $sub || $sub->plan?->slug === 'free') {
            return 'free';
        }

        return match ($sub->status) {
            UserSubscription::STATUS_TRIALING => 'trial',
            UserSubscription::STATUS_PAST_DUE => 'risco_de_churn',
            UserSubscription::STATUS_ACTIVE   => $this->isPowerUser($user->id) ? 'power_users' : 'active',
            default                           => 'free',
        };
    }

    public function countsBySegment(): array
    {
        $users = User::with('currentOpenSubscription.plan')->get();

        $segments = [
            'free' => 0,
            'trial' => 0,
            'active' => 0,
            'power_users' => 0,
            'risco_de_churn' => 0,
        ];

        foreach ($users as $user) {
            $segment = $this->segmentForUser($user);
            $segments[$segment]++;
        }

        return $segments;
    }

    private function isPowerUser(int $userId): bool
    {
        $usage = (int) FeatureUsage::query()
            ->where('user_id', $userId)
            ->where('period', now()->format('Y-m'))
            ->sum('usage_count');

        return $usage >= 200;
    }
}
