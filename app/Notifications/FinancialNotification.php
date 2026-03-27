<?php

namespace App\Notifications;

use App\Models\Notification;
use App\Models\NotificationPreference;

abstract class FinancialNotification
{
    abstract public function type(): string;

    abstract public function severity(): string;

    abstract public function title(): string;

    abstract public function message(): string;

    abstract public function data(): array;

    public function channel(): string
    {
        return 'dashboard';
    }

    /**
     * Persist the notification to the database if enabled for this user.
     */
    public function sendTo(int $userId): ?Notification
    {
        if (! NotificationPreference::isEnabled($userId, $this->type(), $this->channel())) {
            return null;
        }

        return Notification::create([
            'user_id'  => $userId,
            'type'     => $this->type(),
            'severity' => $this->severity(),
            'title'    => $this->title(),
            'message'  => $this->message(),
            'data'     => $this->data(),
            'channel'  => $this->channel(),
        ]);
    }
}
