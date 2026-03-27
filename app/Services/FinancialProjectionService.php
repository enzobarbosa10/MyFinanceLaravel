<?php

namespace App\Services;

use App\Enums\InstallmentItemStatus;
use App\Enums\SubscriptionFrequency;
use App\Enums\TransactionType;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class FinancialProjectionService
{
    private const PROJECTION_HORIZONS = [1, 3, 6];

    public function project(User $user): array
    {
        $currentBalance = $this->getCurrentBalance($user);
        $recurringIncome = $this->getMonthlyRecurringIncome($user);
        $recurringExpenses = $this->getMonthlyRecurringExpenses($user);
        $subscriptionCosts = $this->getMonthlySubscriptionCosts($user);
        $pendingInstallments = $this->getPendingInstallmentsByMonth($user);

        $projections = [];

        foreach (self::PROJECTION_HORIZONS as $months) {
            $projections[$months] = $this->buildProjection(
                $currentBalance,
                $recurringIncome,
                $recurringExpenses,
                $subscriptionCosts,
                $pendingInstallments,
                $months,
            );
        }

        return [
            'current_balance' => round($currentBalance, 2),
            'monthly_recurring_income' => round($recurringIncome, 2),
            'monthly_recurring_expenses' => round($recurringExpenses, 2),
            'monthly_subscription_costs' => round($subscriptionCosts, 2),
            'projections' => $projections,
        ];
    }

    private function getCurrentBalance(User $user): float
    {
        return (float) $user->accounts()->sum('balance');
    }

    private function getMonthlyRecurringIncome(User $user): float
    {
        return (float) $user->transactions()
            ->recurring()
            ->entrada()
            ->where('transaction_at', '>=', now()->subMonths(3))
            ->selectRaw('AVG(amount) as avg_amount')
            ->value('avg_amount') ?? 0;
    }

    private function getMonthlyRecurringExpenses(User $user): float
    {
        return (float) $user->transactions()
            ->recurring()
            ->saida()
            ->where('transaction_at', '>=', now()->subMonths(3))
            ->selectRaw('AVG(amount) as avg_amount')
            ->value('avg_amount') ?? 0;
    }

    private function getMonthlySubscriptionCosts(User $user): float
    {
        $subscriptions = $user->subscriptions()->active()->get();

        return $subscriptions->sum(fn ($sub) => $this->normalizeToMonthly($sub->amount, $sub->frequency));
    }

    private function normalizeToMonthly(float $amount, SubscriptionFrequency $frequency): float
    {
        return match ($frequency) {
            SubscriptionFrequency::Weekly     => $amount * 4,
            SubscriptionFrequency::Biweekly   => $amount * 2,
            SubscriptionFrequency::Monthly    => $amount,
            SubscriptionFrequency::Quarterly  => $amount / 3,
            SubscriptionFrequency::Semiannual => $amount / 6,
            SubscriptionFrequency::Yearly     => $amount / 12,
        };
    }

    /**
     * Agrupa parcelas pendentes por mês (YYYY-MM).
     */
    private function getPendingInstallmentsByMonth(User $user): Collection
    {
        $endDate = now()->addMonths(max(self::PROJECTION_HORIZONS))->endOfMonth();

        return $user->installments()
            ->active()
            ->with(['items' => fn ($q) => $q
                ->pending()
                ->where('due_date', '>=', now()->startOfDay())
                ->where('due_date', '<=', $endDate),
            ])
            ->get()
            ->flatMap(fn ($installment) => $installment->items)
            ->groupBy(fn ($item) => $item->due_date->format('Y-m'))
            ->map(fn ($items) => $items->sum('amount'));
    }

    private function buildProjection(
        float $currentBalance,
        float $recurringIncome,
        float $recurringExpenses,
        float $subscriptionCosts,
        Collection $installmentsByMonth,
        int $months,
    ): array {
        $balance = $currentBalance;
        $monthlyNet = $recurringIncome - $recurringExpenses - $subscriptionCosts;
        $monthlyBreakdown = [];
        $hasNegativeBalance = false;

        for ($i = 1; $i <= $months; $i++) {
            $monthKey = now()->addMonths($i)->format('Y-m');
            $installmentCost = $installmentsByMonth->get($monthKey, 0);

            $balance += $monthlyNet - $installmentCost;

            if ($balance < 0) {
                $hasNegativeBalance = true;
            }

            $monthlyBreakdown[] = [
                'month' => $monthKey,
                'recurring_income' => round($recurringIncome, 2),
                'recurring_expenses' => round($recurringExpenses, 2),
                'subscription_costs' => round($subscriptionCosts, 2),
                'installment_costs' => round($installmentCost, 2),
                'projected_balance' => round($balance, 2),
            ];
        }

        return [
            'horizon_months' => $months,
            'projected_balance' => round($balance, 2),
            'total_installment_costs' => round(
                collect($monthlyBreakdown)->sum('installment_costs'), 2
            ),
            'has_negative_balance' => $hasNegativeBalance,
            'monthly_breakdown' => $monthlyBreakdown,
        ];
    }
}
