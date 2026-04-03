<?php

namespace App\Notifications;

use App\Models\UserSubscription;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentRecoveryNotification extends Notification
{
    use Queueable;

    public function __construct(private readonly UserSubscription $subscription) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage())
            ->subject('Problema no pagamento da assinatura')
            ->line('Detectamos uma falha no ultimo pagamento da sua assinatura.')
            ->line('Tentaremos uma nova cobranca automaticamente.')
            ->line('Caso prefira, voce pode optar por um plano mais economico.');
    }
}
