<?php

namespace App\Services;

use App\Enums\GoalStatus;
use App\Enums\TransactionType;
use App\Events\GoalBehindSchedule;
use App\Events\LowBalanceDetected;
use App\Events\UnusualSpendingDetected;
use App\Models\Account;
use App\Models\Goal;
use App\Models\Notification;
use App\Models\NotificationPreference;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;

class FinancialNotificationService
{
    private const DEFAULT_LOW_BALANCE_THRESHOLD = 100.00;
    private const UNUSUAL_SPENDING_MULTIPLIER = 2.0;
    private const MIN_TRANSACTIONS_FOR_AVERAGE = 3;

    /**
     * Check if a transaction represents unusual spending and fire event if so.
     */
    public function checkUnusualSpending(Transaction $transaction): void
    {
        if ($transaction->type !== TransactionType::Saida) {
            return;
        }

        $user = $transaction->user;

        $average = Transaction::forUser($user->id)
            ->ofType(TransactionType::Saida)
            ->where('category_id', $transaction->category_id)
            ->where('id', '!=', $transaction->id)
            ->where('transaction_at', '>=', now()->subMonths(3))
            ->avg('amount');

        if (! $average) {
            return;
        }

        $count = Transaction::forUser($user->id)
            ->ofType(TransactionType::Saida)
            ->where('category_id', $transaction->category_id)
            ->where('id', '!=', $transaction->id)
            ->where('transaction_at', '>=', now()->subMonths(3))
            ->count();

        if ($count < self::MIN_TRANSACTIONS_FOR_AVERAGE) {
            return;
        }

        $deviationFactor = $transaction->amount / $average;

        if ($deviationFactor >= self::UNUSUAL_SPENDING_MULTIPLIER
            && $this->shouldNotify($user->id, 'unusual_spending', $transaction->id)
        ) {
            UnusualSpendingDetected::dispatch($user, $transaction, (float) $average, $deviationFactor);
        }
    }

    /**
     * Check if an account has a low balance and fire event if so.
     */
    public function checkLowBalance(Account $account): void
    {
        $user = $account->user;
        $threshold = NotificationPreference::getThreshold($user->id, 'low_balance')
            ?? self::DEFAULT_LOW_BALANCE_THRESHOLD;

        if ((float) $account->balance < $threshold
            && $this->shouldNotify($user->id, 'low_balance', $account->id)
        ) {
            LowBalanceDetected::dispatch($user, $account, (float) $account->balance, $threshold);
        }
    }

    /**
     * Check all active goals for a user and fire events for behind-schedule ones.
     */
    public function checkGoalsBehindSchedule(User $user): void
    {
        $goals = $user->goals()->active()->whereNotNull('deadline')->get();

        foreach ($goals as $goal) {
            $this->evaluateGoalProgress($user, $goal);
        }
    }

    /**
     * Evaluate a single goal's progress against its expected timeline.
     */
    private function evaluateGoalProgress(User $user, Goal $goal): void
    {
        $expectedProgress = $this->calculateExpectedProgress($goal);
        $actualProgress = $goal->progressPercentage();

        // Only fire if behind by at least 10 percentage points
        if ($expectedProgress - $actualProgress >= 10
            && $this->shouldNotify($user->id, 'goal_behind', $goal->id)
        ) {
            GoalBehindSchedule::dispatch($user, $goal, $expectedProgress, $actualProgress);
        }
    }

    /**
     * Calculate expected progress based on time elapsed since creation.
     */
    private function calculateExpectedProgress(Goal $goal): float
    {
        $start = $goal->created_at;
        $end = $goal->deadline;
        $now = now();

        if ($now->greaterThanOrEqualTo($end)) {
            return 100.0;
        }

        $totalDays = $start->diffInDays($end);
        if ($totalDays === 0) {
            return 100.0;
        }

        $elapsedDays = $start->diffInDays($now);

        return min(100, round(($elapsedDays / $totalDays) * 100, 1));
    }

    /**
     * Avoid duplicate notifications: only notify once per type+entity per day.
     */
    private function shouldNotify(int $userId, string $type, int $relatedId): bool
    {
        return ! Notification::where('user_id', $userId)
            ->where('type', $type)
            ->where('data->'.($this->getRelatedKey($type)), $relatedId)
            ->where('created_at', '>=', now()->startOfDay())
            ->exists();
    }

    private function getRelatedKey(string $type): string
    {
        return match ($type) {
            'unusual_spending' => 'transaction_id',
            'low_balance'      => 'account_id',
            'goal_behind'      => 'goal_id',
            default            => 'id',
        };
    }
}
