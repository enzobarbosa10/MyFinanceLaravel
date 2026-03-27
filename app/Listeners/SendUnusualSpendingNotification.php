<?php

namespace App\Listeners;

use App\Events\UnusualSpendingDetected;
use App\Notifications\UnusualSpendingNotification;

class SendUnusualSpendingNotification
{
    public function handle(UnusualSpendingDetected $event): void
    {
        $notification = new UnusualSpendingNotification(
            $event->transaction,
            $event->averageAmount,
            $event->deviationFactor,
        );

        $notification->sendTo($event->user->id);
    }
}
