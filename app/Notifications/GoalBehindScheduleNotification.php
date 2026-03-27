<?php

namespace App\Notifications;

use App\Models\Goal;

class GoalBehindScheduleNotification extends FinancialNotification
{
    public function __construct(
        private readonly Goal $goal,
        private readonly float $expectedProgress,
        private readonly float $actualProgress,
    ) {}

    public function type(): string
    {
        return 'goal_behind';
    }

    public function severity(): string
    {
        $gap = $this->expectedProgress - $this->actualProgress;
        return $gap >= 30 ? 'critical' : 'warning';
    }

    public function title(): string
    {
        return 'Meta atrasada';
    }

    public function message(): string
    {
        return sprintf(
            'A meta "%s" está com %.1f%% de progresso, mas deveria estar em %.1f%%. Prazo: %s.',
            $this->goal->name,
            $this->actualProgress,
            $this->expectedProgress,
            $this->goal->deadline->format('d/m/Y'),
        );
    }

    public function data(): array
    {
        return [
            'goal_id'           => $this->goal->id,
            'expected_progress' => $this->expectedProgress,
            'actual_progress'   => $this->actualProgress,
            'deadline'          => $this->goal->deadline->toDateString(),
            'remaining_amount'  => $this->goal->remaining_amount,
        ];
    }
}
