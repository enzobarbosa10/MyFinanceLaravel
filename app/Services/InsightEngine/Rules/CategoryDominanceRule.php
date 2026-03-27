<?php

namespace App\Services\InsightEngine\Rules;

use App\Enums\InsightType;
use App\Enums\TransactionType;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use App\Services\InsightEngine\Contracts\InsightRule;
use Illuminate\Support\Collection;

/**
 * Gera sugestão quando uma única categoria representa mais de 40%
 * do total de gastos do mês.
 */
class CategoryDominanceRule implements InsightRule
{
    private const THRESHOLD = 0.40; // 40%

    public function evaluate(User $user): Collection
    {
        $currentMonth = now()->format('Y-m');

        $totalSpending = (float) Transaction::forUser($user->id)
            ->forMonth($currentMonth)
            ->where('type', TransactionType::Saida)
            ->sum('amount');

        if ($totalSpending == 0) {
            return collect();
        }

        $spendingByCategory = Transaction::forUser($user->id)
            ->forMonth($currentMonth)
            ->where('type', TransactionType::Saida)
            ->whereNotNull('category_id')
            ->selectRaw('category_id, SUM(amount) as total')
            ->groupBy('category_id')
            ->pluck('total', 'category_id');

        $insights = collect();

        foreach ($spendingByCategory as $categoryId => $categoryTotal) {
            $ratio = (float) $categoryTotal / $totalSpending;

            if ($ratio <= self::THRESHOLD) {
                continue;
            }

            $category = Category::find($categoryId);
            if (!$category) {
                continue;
            }

            $percentFormatted = number_format($ratio * 100, 1, ',', '.');

            $insights->push([
                'type'          => InsightType::Suggestion->value,
                'title'         => "Concentração em {$category->name}",
                'message'       => "A categoria \"{$category->name}\" representa {$percentFormatted}% dos seus gastos "
                                 . "este mês (R$ " . number_format($categoryTotal, 2, ',', '.') . " de "
                                 . "R$ " . number_format($totalSpending, 2, ',', '.') . "). "
                                 . "Considere redistribuir seus gastos para manter o equilíbrio financeiro.",
                'impact_value'  => round((float) $categoryTotal, 2),
                'related_type'  => Category::class,
                'related_id'    => $categoryId,
                'expires_at'    => now()->endOfMonth(),
            ]);
        }

        return $insights;
    }
}
