<?php

namespace App\Services;

use App\Models\FeatureUsage;
use App\Models\UserSubscription;
use App\Notifications\TrialNudgeNotification;

class TrialOptimizationService
{
    public function optimize(): array
    {
        $subs = UserSubscription::query()
            ->with(['user', 'plan'])
            ->where('status', UserSubscription::STATUS_TRIALING)
            ->get();

        $nudges = 0;
        $unlockApplied = 0;

        foreach ($subs as $sub) {
            if (! $sub->user) {
                continue;
            }

            $usage = (int) FeatureUsage::query()
                ->where('user_id', $sub->user_id)
                ->where('period', now()->format('Y-m'))
                ->sum('usage_count');

            if ($usage >= 50) {
                $sub->user->notify(new TrialNudgeNotification('high_engagement'));
                $nudges++;
            }

            if ($sub->trial_ends_at && $sub->trial_ends_at->diffInDays(now(), false) >= -2) {
                cache()->put("trial:unlock:{$sub->user_id}:ai_assistant", true, now()->addDays(2));
                $sub->user->notify(new TrialNudgeNotification('trial_ending'));
                $unlockApplied++;
            }
        }

        return [
            'trial_users' => $subs->count(),
            'nudges' => $nudges,
            'strategic_unlocks' => $unlockApplied,
        ];
    }
}
