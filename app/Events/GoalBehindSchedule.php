<?php

namespace App\Events;

use App\Models\Goal;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class GoalBehindSchedule
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly User $user,
        public readonly Goal $goal,
        public readonly float $expectedProgress,
        public readonly float $actualProgress,
    ) {}
}
