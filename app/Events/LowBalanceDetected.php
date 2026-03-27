<?php

namespace App\Events;

use App\Models\Account;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LowBalanceDetected
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly User $user,
        public readonly Account $account,
        public readonly float $currentBalance,
        public readonly float $threshold,
    ) {}
}
