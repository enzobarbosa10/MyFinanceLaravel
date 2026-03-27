<?php

namespace App\Services\InsightEngine\Rules;

use App\Enums\InsightType;
use App\Enums\TransactionType;
use App\Models\Account;
use App\Models\Subscription;
use App\Models\Transaction;
use App\Models\User;
use App\Services\InsightEngine\Contracts\InsightRule;
use Illuminate\Support\Collection;

/**
 * Gera risco quando o saldo projetado para o final do mês é negativo.
 * Calcula: saldo atual - gastos fixos restantes (assinaturas ativas).
 */
class NegativeBalanceRule implements InsightRule
{
    public function evaluate(User $user): Collection
    {
        $totalBalance = (float) Account::where('user_id', $user->id)->sum('balance');

        $projectedExpenses = $this->getProjectedExpenses($user);

        $projectedBalance = $totalBalance - $projectedExpenses;

        if ($projectedBalance >= 0) {
            return collect();
        }

        return collect([[
            'type'          => InsightType::Risk->value,
            'title'         => 'Saldo projetado negativo',
            'message'       => "Seu saldo atual é R$ " . number_format($totalBalance, 2, ',', '.')
                             . " e você ainda tem R$ " . number_format($projectedExpenses, 2, ',', '.')
                             . " em despesas previstas para este mês. "
                             . "O saldo projetado ficará em R$ " . number_format($projectedBalance, 2, ',', '.')
                             . ". Revise seus gastos para evitar ficar no vermelho.",
            'impact_value'  => round(abs($projectedBalance), 2),
            'related_type'  => null,
            'related_id'    => null,
            'expires_at'    => now()->endOfMonth(),
        ]]);
    }

    /**
     * Estima despesas restantes com base nas assinaturas ativas
     * cujo próximo vencimento ainda está neste mês.
     */
    private function getProjectedExpenses(User $user): float
    {
        return (float) Subscription::where('user_id', $user->id)
            ->where('is_active', true)
            ->where('next_billing_date', '>=', now())
            ->where('next_billing_date', '<=', now()->endOfMonth())
            ->sum('amount');
    }
}
