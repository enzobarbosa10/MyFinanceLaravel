<?php

namespace App\Notifications;

class UpsellSuggestionNotification extends FinancialNotification
{
    public function __construct(
        private readonly string $fromPlan,
        private readonly string $toPlan,
        private readonly string $reason,
    ) {}

    public function type(): string
    {
        return 'upsell_suggestion';
    }

    public function severity(): string
    {
        return 'info';
    }

    public function title(): string
    {
        return 'Upgrade recomendado';
    }

    public function message(): string
    {
        return "Seu uso atual indica ganho de valor ao migrar de {$this->fromPlan} para {$this->toPlan}.";
    }

    public function data(): array
    {
        return [
            'from_plan' => $this->fromPlan,
            'to_plan' => $this->toPlan,
            'reason' => $this->reason,
        ];
    }
}
