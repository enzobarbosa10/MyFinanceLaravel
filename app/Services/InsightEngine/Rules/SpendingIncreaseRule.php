<?php

namespace App\Services\InsightEngine\Rules;

use App\Enums\InsightType;
use App\Enums\TransactionType;
use App\Models\Transaction;
use App\Models\User;
use App\Services\InsightEngine\Contracts\InsightRule;
use Illuminate\Support\Collection;

/**
 * Gera alerta quando os gastos do mês atual aumentaram mais de 30%
 * em relação ao mês anterior.
 */
class SpendingIncreaseRule implements InsightRule
{
    private const THRESHOLD = 0.30; // 30%

    public function evaluate(User $user): Collection
    {
        $currentMonth = now()->format('Y-m');
        $previousMonth = now()->subMonthNoOverflow()->format('Y-m');

        $currentSpending = $this->getMonthlySpending($user->id, $currentMonth);
        $previousSpending = $this->getMonthlySpending($user->id, $previousMonth);

        // Sem dados do mês anterior para comparar
        if ($previousSpending == 0) {
            return collect();
        }

        $increaseRate = ($currentSpending - $previousSpending) / $previousSpending;

        if ($increaseRate <= self::THRESHOLD) {
            return collect();
        }

        $percentFormatted = number_format($increaseRate * 100, 1, ',', '.');

        return collect([[
            'type'          => InsightType::Alert->value,
            'title'         => 'Gastos em alta!',
            'message'       => "Seus gastos aumentaram {$percentFormatted}% em relação ao mês anterior "
                             . "(de R$ " . number_format($previousSpending, 2, ',', '.') . " para "
                             . "R$ " . number_format($currentSpending, 2, ',', '.') . ").",
            'impact_value'  => round($currentSpending - $previousSpending, 2),
            'related_type'  => null,
            'related_id'    => null,
            'expires_at'    => now()->endOfMonth(),
        ]]);
    }

    private function getMonthlySpending(int $userId, string $month): float
    {
        return (float) Transaction::forUser($userId)
            ->forMonth($month)
            ->where('type', TransactionType::Saida)
            ->sum('amount');
    }
}
