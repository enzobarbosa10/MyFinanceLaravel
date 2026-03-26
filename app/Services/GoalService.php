<?php

namespace App\Services;

use App\Enums\GoalStatus;
use App\Models\Goal;
use App\Models\GoalContribution;
use Illuminate\Support\Facades\DB;

class GoalService
{
    public function contribute(Goal $goal, float $amount, ?string $notes = null): GoalContribution
    {
        return DB::transaction(function () use ($goal, $amount, $notes) {
            $contribution = GoalContribution::create([
                'goal_id' => $goal->id,
                'amount' => $amount,
                'contributed_at' => now()->toDateString(),
                'notes' => $notes,
            ]);

            $goal->increment('current_amount', $amount);

            if ($goal->fresh()->current_amount >= $goal->target_amount) {
                $goal->update(['status' => GoalStatus::Completed]);
            }

            return $contribution;
        });
    }
}
