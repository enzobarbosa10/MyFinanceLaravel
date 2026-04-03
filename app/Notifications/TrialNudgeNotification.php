<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TrialNudgeNotification extends Notification
{
    use Queueable;

    public function __construct(private readonly string $scenario) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $message = match ($this->scenario) {
            'high_engagement' => 'Voce esta aproveitando muito o trial. Ative um plano pago para manter seu ritmo.',
            default => 'Seu trial esta terminando. Aproveite beneficios extras para concluir sua ativacao.',
        };

        return (new MailMessage())
            ->subject('Aproveite melhor seu trial')
            ->line($message);
    }
}
