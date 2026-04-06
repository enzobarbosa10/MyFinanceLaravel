<?php

namespace App\Services\Concerns;

use App\Exceptions\InvalidSubscriptionTransitionException;
use App\Models\SubscriptionLog;
use App\Models\User;
use App\Models\UserSubscription;
use Illuminate\Support\Facades\Log;

trait HandlesSubscriptionLifecycle
{
    private function lockCurrentOpenSubscription(User $user): ?UserSubscription
    {
        return UserSubscription::query()
            ->where('user_id', $user->id)
            ->whereIn('status', UserSubscription::OPEN_STATUSES)
            ->latest('id')
            ->lockForUpdate()
            ->first();
    }

    private function cancelLockedSubscription(?UserSubscription $subscription): void
    {
        if (! $subscription) {
            return;
        }

        $subscription->update([
            'status' => UserSubscription::STATUS_CANCELED,
            'canceled_at' => now(),
        ]);
    }

    private function applyWebhookEvent(UserSubscription $subscription, array $normalized): void
    {
        $event = $normalized['event'] ?? '';

        if ($event === 'payment_success') {
            $this->transitionSubscriptionStatus($subscription, UserSubscription::STATUS_ACTIVE);
            return;
        }

        if ($event === 'payment_failed') {
            $this->transitionSubscriptionStatus($subscription, UserSubscription::STATUS_PAST_DUE);
            return;
        }

        if ($event === 'subscription_canceled') {
            $this->transitionSubscriptionStatus($subscription, UserSubscription::STATUS_CANCELED);
        }
    }

    private function transitionSubscriptionStatus(UserSubscription $subscription, string $nextStatus): void
    {
        if ($subscription->status === $nextStatus) {
            return;
        }

        if (! $subscription->canTransitionTo($nextStatus)) {
            throw new InvalidSubscriptionTransitionException(
                "Transição inválida de {$subscription->status} para {$nextStatus}."
            );
        }

        $subscription->update([
            'status' => $nextStatus,
            'canceled_at' => $nextStatus === UserSubscription::STATUS_CANCELED ? now() : $subscription->canceled_at,
        ]);
    }

    private function logFinancialEvent(string $event, User $user, UserSubscription $subscription, array $context = []): void
    {
        Log::info($event, array_merge([
            'user_id' => $user->id,
            'subscription_id' => $subscription->id,
            'gateway' => $subscription->gateway,
            'event_type' => $event,
            'plan_id' => $subscription->plan_id,
            'status' => $subscription->status,
        ], $context));
    }

    private function writeSubscriptionAudit(UserSubscription $subscription, string $action, array $payload): void
    {
        SubscriptionLog::create([
            'subscription_id' => $subscription->id,
            'user_id' => $subscription->user_id,
            'action' => $action,
            'gateway' => $subscription->gateway,
            'payload' => $payload,
            'logged_at' => now(),
        ]);
    }
}
