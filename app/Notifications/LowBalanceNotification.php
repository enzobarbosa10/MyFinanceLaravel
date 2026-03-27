<?php

namespace App\Notifications;

use App\Models\Account;

class LowBalanceNotification extends FinancialNotification
{
    public function __construct(
        private readonly Account $account,
        private readonly float $currentBalance,
        private readonly float $threshold,
    ) {}

    public function type(): string
    {
        return 'low_balance';
    }

    public function severity(): string
    {
        return $this->currentBalance <= 0 ? 'critical' : 'warning';
    }

    public function title(): string
    {
        return 'Saldo baixo na conta';
    }

    public function message(): string
    {
        return sprintf(
            'A conta "%s" está com saldo de R$ %s, abaixo do limite de R$ %s.',
            $this->account->name,
            number_format($this->currentBalance, 2, ',', '.'),
            number_format($this->threshold, 2, ',', '.'),
        );
    }

    public function data(): array
    {
        return [
            'account_id'      => $this->account->id,
            'current_balance' => $this->currentBalance,
            'threshold'       => $this->threshold,
        ];
    }
}
