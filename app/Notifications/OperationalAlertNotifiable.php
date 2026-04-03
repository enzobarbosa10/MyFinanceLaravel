<?php

namespace App\Notifications;

class OperationalAlertNotifiable
{
    public function routeNotificationForMail(): string
    {
        return (string) config('services.operations.alert_email', config('mail.from.address'));
    }

    public function routeNotificationForSlack(): ?string
    {
        return config('services.slack.notifications.channel');
    }
}
