<?php

namespace App\Services;

use App\Models\UsageAggregate;
use App\Models\UsageRecord;
use Illuminate\Support\Facades\DB;

class UsageAggregatorService
{
    public function aggregate(?string $period = null): int
    {
        $billingPeriod = $period ?? now()->format('Y-m');

        $rows = UsageRecord::query()
            ->where('billing_period', $billingPeriod)
            ->where('is_aggregated', false)
            ->select('user_id', 'plan_id', 'feature', 'billing_period')
            ->selectRaw('SUM(quantity) as total_quantity')
            ->groupBy('user_id', 'plan_id', 'feature', 'billing_period')
            ->get();

        DB::transaction(function () use ($rows, $billingPeriod) {
            foreach ($rows as $row) {
                UsageAggregate::updateOrCreate([
                    'user_id' => $row->user_id,
                    'feature' => $row->feature,
                    'billing_period' => $billingPeriod,
                ], [
                    'plan_id' => $row->plan_id,
                    'total_quantity' => $row->total_quantity,
                    'aggregated_at' => now(),
                    'metadata' => [],
                ]);
            }

            UsageRecord::query()
                ->where('billing_period', $billingPeriod)
                ->where('is_aggregated', false)
                ->update(['is_aggregated' => true]);
        });

        return $rows->count();
    }
}
