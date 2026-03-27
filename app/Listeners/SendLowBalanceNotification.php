<?php

namespace App\Listeners;

use App\Events\LowBalanceDetected;
use App\Notifications\LowBalanceNotification;

class SendLowBalanceNotification
{
    public function handle(LowBalanceDetected $event): void
    {
        $notification = new LowBalanceNotification(
            $event->account,
            $event->currentBalance,
            $event->threshold,
        );

        $notification->sendTo($event->user->id);
    }
}
