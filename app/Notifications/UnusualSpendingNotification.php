<?php

namespace App\Notifications;

use App\Models\Transaction;

class UnusualSpendingNotification extends FinancialNotification
{
    public function __construct(
        private readonly Transaction $transaction,
        private readonly float $averageAmount,
        private readonly float $deviationFactor,
    ) {}

    public function type(): string
    {
        return 'unusual_spending';
    }

    public function severity(): string
    {
        return $this->deviationFactor >= 3 ? 'critical' : 'warning';
    }

    public function title(): string
    {
        return 'Gasto fora do padrão detectado';
    }

    public function message(): string
    {
        return sprintf(
            'Uma transação de R$ %s está %.1fx acima da sua média de R$ %s na categoria "%s".',
            number_format($this->transaction->amount, 2, ',', '.'),
            $this->deviationFactor,
            number_format($this->averageAmount, 2, ',', '.'),
            $this->transaction->category?->name ?? 'Sem categoria',
        );
    }

    public function data(): array
    {
        return [
            'transaction_id' => $this->transaction->id,
            'amount'         => $this->transaction->amount,
            'average_amount' => $this->averageAmount,
            'deviation'      => $this->deviationFactor,
            'category_id'    => $this->transaction->category_id,
        ];
    }
}
