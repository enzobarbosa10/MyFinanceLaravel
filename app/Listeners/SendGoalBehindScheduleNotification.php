<?php

namespace App\Listeners;

use App\Events\GoalBehindSchedule;
use App\Notifications\GoalBehindScheduleNotification;

class SendGoalBehindScheduleNotification
{
    public function handle(GoalBehindSchedule $event): void
    {
        $notification = new GoalBehindScheduleNotification(
            $event->goal,
            $event->expectedProgress,
            $event->actualProgress,
        );

        $notification->sendTo($event->user->id);
    }
}
