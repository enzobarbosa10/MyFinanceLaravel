<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\Plan;
use App\Models\UserSubscription;
use App\Notifications\PaymentRecoveryNotification;
use App\Services\Payments\PaymentGatewayResolver;
use Illuminate\Support\Facades\DB;

class ChurnRecoveryService
{
    public function __construct(
        private readonly PaymentGatewayResolver $gatewayResolver,
        private readonly PlanService $planService,
    ) {}

    public function recoverPastDueSubscriptions(): array
    {
        $subscriptions = UserSubscription::query()
            ->with(['user', 'plan'])
            ->where('status', UserSubscription::STATUS_PAST_DUE)
            ->get();

        $recovered = 0;
        $downgraded = 0;
        $notified = 0;

        foreach ($subscriptions as $subscription) {
            $subscription->user?->notify(new PaymentRecoveryNotification($subscription));
            $notified++;

            $payment = Payment::query()
                ->where('user_subscription_id', $subscription->id)
                ->latest('id')
                ->first();

            if (! $payment || ! $subscription->gateway) {
                continue;
            }

            $response = $payment->gateway_response ?? [];
            $retries = (int) ($response['retry_count'] ?? 0);

            if ($retries >= 3) {
                $downgraded += $this->attemptDowngradeToFree($subscription);
                continue;
            }

            $recovered += $this->attemptChargeRecovery($subscription, $payment, $retries);
        }

        return [
            'past_due' => $subscriptions->count(),
            'notified' => $notified,
            'recovered' => $recovered,
            'downgraded' => $downgraded,
        ];
    }

    private function attemptDowngradeToFree(UserSubscription $subscription): int
    {
        $free = Plan::free();
        if (! $free || ! $subscription->user) {
            return 0;
        }

        $this->planService->subscribe($subscription->user, $free, null);

        return 1;
    }

    private function attemptChargeRecovery(UserSubscription $subscription, Payment $payment, int $retries): int
    {
        $gateway = $this->gatewayResolver->resolve($subscription->gateway);
        if (! $gateway || ! $subscription->user || ! $subscription->plan) {
            return 0;
        }

        $charge = $gateway->charge($subscription->user, $subscription->plan);

        return DB::transaction(function () use ($subscription, $payment, $charge, $retries) {
            if (($charge['status'] ?? 'pending') === 'paid') {
                $subscription->update(['status' => UserSubscription::STATUS_ACTIVE]);
                $payment->update([
                    'status' => Payment::STATUS_PAID,
                    'paid_at' => now(),
                    'gateway_response' => array_merge($payment->gateway_response ?? [], $charge),
                ]);

                return 1;
            }

            $payment->update([
                'status' => Payment::STATUS_FAILED,
                'gateway_response' => array_merge($payment->gateway_response ?? [], $charge, [
                    'retry_count' => $retries + 1,
                ]),
            ]);

            return 0;
        });
    }
}
