<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\Plan;
use App\Models\UserSubscription;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class BillingMetricsService
{
    public function metrics(): array
    {
        return Cache::remember('billing:metrics:v1', now()->addMinutes(10), function () {
            $mrr = $this->calculateMrr();
            $arr = $mrr * 12;

            return [
                'mrr' => $mrr,
                'arr' => $arr,
                'churn_rate' => $this->calculateChurnRate(),
                'ltv' => $this->calculateLtv($mrr),
                'revenue_by_plan' => $this->revenueByPlan(),
                'trial_to_paid_conversion' => $this->trialToPaidConversion(),
                'generated_at' => now()->toISOString(),
            ];
        });
    }

    private function calculateMrr(): float
    {
        return round((float) UserSubscription::query()
            ->join('plans', 'plans.id', '=', 'user_subscriptions.plan_id')
            ->where('user_subscriptions.status', UserSubscription::STATUS_ACTIVE)
            ->selectRaw("SUM(CASE WHEN plans.billing_cycle = 'yearly' THEN plans.price / 12 ELSE plans.price END) as mrr")
            ->value('mrr'), 2);
    }

    private function calculateChurnRate(): float
    {
        $start = now()->subMonth()->startOfMonth();
        $end = now()->subMonth()->endOfMonth();

        $canceled = UserSubscription::query()
            ->whereBetween('canceled_at', [$start, $end])
            ->count();

        $activeBase = max(UserSubscription::query()
            ->where('created_at', '<=', $start)
            ->whereIn('status', [UserSubscription::STATUS_ACTIVE, UserSubscription::STATUS_TRIALING, UserSubscription::STATUS_PAST_DUE])
            ->count(), 1);

        return round(($canceled / $activeBase) * 100, 2);
    }

    private function calculateLtv(float $mrr): float
    {
        $activeCustomers = max(UserSubscription::query()
            ->whereIn('status', [UserSubscription::STATUS_ACTIVE, UserSubscription::STATUS_TRIALING, UserSubscription::STATUS_PAST_DUE])
            ->distinct('user_id')
            ->count('user_id'), 1);

        $arpa = $mrr / $activeCustomers;
        $monthlyChurn = $this->calculateChurnRate() / 100;

        if ($monthlyChurn <= 0) {
            return round($arpa * 24, 2);
        }

        return round($arpa / $monthlyChurn, 2);
    }

    private function revenueByPlan(): array
    {
        return Plan::query()
            ->leftJoin('user_subscriptions', 'user_subscriptions.plan_id', '=', 'plans.id')
            ->whereIn('user_subscriptions.status', [UserSubscription::STATUS_ACTIVE, UserSubscription::STATUS_TRIALING, UserSubscription::STATUS_PAST_DUE])
            ->groupBy('plans.id', 'plans.slug', 'plans.name')
            ->select('plans.slug', 'plans.name')
            ->selectRaw('COUNT(user_subscriptions.id) as subscribers')
            ->selectRaw('SUM(plans.price) as revenue')
            ->get()
            ->map(fn ($row) => [
                'plan' => $row->slug,
                'name' => $row->name,
                'subscribers' => (int) $row->subscribers,
                'revenue' => round((float) $row->revenue, 2),
            ])
            ->all();
    }

    private function trialToPaidConversion(): float
    {
        $trialUsers = UserSubscription::query()
            ->whereNotNull('trial_ends_at')
            ->distinct('user_id')
            ->count('user_id');

        if ($trialUsers === 0) {
            return 0.0;
        }

        $converted = DB::table('user_subscriptions as trials')
            ->join('user_subscriptions as paid', 'paid.user_id', '=', 'trials.user_id')
            ->whereNotNull('trials.trial_ends_at')
            ->where('paid.status', UserSubscription::STATUS_ACTIVE)
            ->whereColumn('paid.created_at', '>', 'trials.created_at')
            ->distinct('trials.user_id')
            ->count('trials.user_id');

        return round(($converted / $trialUsers) * 100, 2);
    }
}
