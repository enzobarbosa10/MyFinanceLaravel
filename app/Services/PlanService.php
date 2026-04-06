<?php

namespace App\Services;

use App\Contracts\PaymentGatewayInterface;
use App\Events\PaymentConfirmed;
use App\Events\SubscriptionCreated;
use App\Exceptions\InvalidSubscriptionTransitionException;
use App\Exceptions\PaymentWebhookException;
use App\Exceptions\PaymentGatewayUnavailableException;
use App\Exceptions\PlanConfigurationException;
use App\Models\Payment;
use App\Models\Plan;
use App\Models\ProcessedEvent;
use App\Models\SubscriptionLog;
use App\Models\User;
use App\Models\UserSubscription;
use App\Services\Concerns\HandlesCircuitBreaker;
use App\Services\Concerns\HandlesSubscriptionLifecycle;
use App\Services\OperationalAlertService;
use App\Services\Payments\SubscriptionTelemetryService;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;
use Throwable;

class PlanService
{
    use HandlesCircuitBreaker;
    use HandlesSubscriptionLifecycle;
    public function __construct(
        private readonly SubscriptionTelemetryService $telemetry,
        private readonly OperationalAlertService $alertService,
    ) {}

    /**
     * Inscreve o usuário em um plano.
     */
    public function subscribe(User $user, Plan $plan, ?PaymentGatewayInterface $gateway = null): UserSubscription
    {
        return DB::transaction(function () use ($user, $plan, $gateway) {
            $current = $this->lockCurrentOpenSubscription($user);
            $this->cancelLockedSubscription($current);

            $isFree = $plan->price <= 0;
            $hasTrial = $plan->trial_days > 0 && ! $user->had_trial;
            $requiresPayment = ! $isFree && ! $hasTrial;

            $this->assertGatewayForPaidPlan($requiresPayment, $gateway, 'Gateway é obrigatório para planos pagos sem trial.');

            $status = $this->resolveSubscriptionStatus($hasTrial, $requiresPayment);

            $gatewaySubscription = $requiresPayment
                ? $this->runGatewayCall(fn () => $gateway->createSubscription($user, $plan), $gateway->name(), 'create_subscription')
                : null;

            $subscription = UserSubscription::create([
                'user_id' => $user->id,
                'plan_id' => $plan->id,
                'status' => $status,
                'trial_ends_at' => $hasTrial ? now()->addDays($plan->trial_days) : null,
                'starts_at' => now(),
                'expires_at' => $this->calculateExpiry($plan),
                'gateway' => $gateway?->name(),
                'gateway_subscription_id' => $gatewaySubscription['id'] ?? null,
            ]);

            if ($hasTrial) {
                $user->forceFill(['had_trial' => true])->save();
            }

            DB::table('subscription_history')->insert([
                'user_id' => $user->id,
                'from_plan_id' => $current?->plan_id,
                'to_plan_id' => $plan->id,
                'action' => 'subscribe',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            if ($requiresPayment) {
                $this->processPayment($user, $subscription, $plan, $gateway);
            }

            SubscriptionCreated::dispatch($subscription);
            $this->telemetry->increment('subscriptions_created_total');
            $this->logFinancialEvent('subscription.created', $user, $subscription, [
                'plan_slug' => $plan->slug,
                'requires_payment' => $requiresPayment,
                'status_before' => $current?->status,
                'status_after' => $subscription->status,
            ]);
            $this->writeSubscriptionAudit($subscription, 'created', [
                'plan_slug' => $plan->slug,
                'requires_payment' => $requiresPayment,
            ]);

            return $subscription;
        });
    }

    /**
     * Upgrade/downgrade de plano.
     */
    public function changePlan(User $user, Plan $newPlan, ?PaymentGatewayInterface $gateway = null): UserSubscription
    {
        return DB::transaction(function () use ($user, $newPlan, $gateway) {
            $current = $this->lockCurrentOpenSubscription($user);
            $currentPlan = $current?->plan;
            $action = $this->determineAction($currentPlan, $newPlan);
            $this->cancelLockedSubscription($current);

            $requiresPayment = $newPlan->price > 0;
            $this->assertGatewayForPaidPlan($requiresPayment, $gateway, 'Gateway é obrigatório para upgrade/downgrade para plano pago.');

            $gatewaySubscription = $requiresPayment
                ? $this->runGatewayCall(fn () => $gateway->createSubscription($user, $newPlan), $gateway->name(), 'create_subscription')
                : null;

            $subscription = UserSubscription::create([
                'user_id' => $user->id,
                'plan_id' => $newPlan->id,
                'status' => $requiresPayment ? UserSubscription::STATUS_PENDING : UserSubscription::STATUS_ACTIVE,
                'starts_at' => now(),
                'expires_at' => $this->calculateExpiry($newPlan),
                'gateway' => $gateway?->name(),
                'gateway_subscription_id' => $gatewaySubscription['id'] ?? null,
            ]);

            DB::table('subscription_history')->insert([
                'user_id' => $user->id,
                'from_plan_id' => $currentPlan?->id,
                'to_plan_id' => $newPlan->id,
                'action' => $action,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            if ($requiresPayment) {
                $this->processPayment($user, $subscription, $newPlan, $gateway);
            }

            SubscriptionCreated::dispatch($subscription);
            $this->telemetry->increment('subscriptions_created_total');
            $this->logFinancialEvent('subscription.changed', $user, $subscription, [
                'action' => $action,
                'from_plan' => $currentPlan?->slug,
                'to_plan' => $newPlan->slug,
                'status_before' => $current?->status,
                'status_after' => $subscription->status,
            ]);
            $this->writeSubscriptionAudit($subscription, 'changed', [
                'action' => $action,
                'from_plan' => $currentPlan?->slug,
                'to_plan' => $newPlan->slug,
            ]);

            return $subscription;
        });
    }

    /**
     * Cancela a assinatura ativa do usuário.
     */
    public function cancel(User $user): bool
    {
        return DB::transaction(function () use ($user) {
            $sub = $this->lockCurrentOpenSubscription($user);
            if (! $sub) {
                return false;
            }

            $sub->update([
                'status' => UserSubscription::STATUS_CANCELED,
                'canceled_at' => now(),
            ]);

            DB::table('subscription_history')->insert([
                'user_id' => $user->id,
                'from_plan_id' => $sub->plan_id,
                'to_plan_id' => $sub->plan_id,
                'action' => 'cancel',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $this->logFinancialEvent('subscription.canceled', $user, $sub);
            $this->writeSubscriptionAudit($sub, 'canceled', []);

            return true;
        });
    }

    public function current(User $user): ?UserSubscription
    {
        return UserSubscription::query()
            ->where('user_id', $user->id)
            ->whereIn('status', UserSubscription::OPEN_STATUSES)
            ->latest('id')
            ->first();
    }

    public function handlePaymentWebhook(PaymentGatewayInterface $gateway, array $payload): void
    {
        $normalized = $gateway->handleWebhook($payload);
        $eventId = $normalized['event_id'] ?? hash('sha256', json_encode($payload));
        $eventType = $normalized['event_type'] ?? $normalized['event'] ?? 'unknown';

        if (! $this->registerProcessedEvent($gateway->name(), (string) $eventId, (string) $eventType, $payload)) {
            Log::info('payment.webhook.duplicate_event', [
                'gateway' => $gateway->name(),
                'event_id' => $eventId,
                'event_type' => $eventType,
            ]);
            return;
        }

        $subscriptionId = $normalized['subscription_id'] ?? null;
        if (! $subscriptionId) {
            throw new PaymentWebhookException('Webhook sem subscription_id.');
        }

        DB::transaction(function () use ($subscriptionId, $normalized, $gateway, $eventId, $eventType) {
            $subscription = UserSubscription::where('gateway_subscription_id', $subscriptionId)
                ->lockForUpdate()
                ->first();

            if (! $subscription) {
                Log::warning('payment.webhook.subscription_not_found', [
                    'gateway' => $gateway->name(),
                    'subscription_id' => $subscriptionId,
                    'event_type' => $eventType,
                ]);
                return;
            }

            $statusBefore = $subscription->status;
            $this->applyWebhookEvent($subscription, $normalized);
            $statusAfter = $subscription->status;

            $payment = Payment::query()
                ->where('user_subscription_id', $subscription->id)
                ->latest('id')
                ->first();

            if ($payment && ($normalized['event'] ?? '') === 'payment_success') {
                $payment->update([
                    'status' => Payment::STATUS_PAID,
                    'paid_at' => now(),
                    'gateway_payment_id' => $normalized['payment_id'] ?? $payment->gateway_payment_id,
                    'gateway_response' => $normalized,
                ]);

                PaymentConfirmed::dispatch($payment);
                $this->telemetry->increment('payments_confirmed_total');
            }

            if ($payment && ($normalized['event'] ?? '') === 'payment_failed') {
                $payment->update([
                    'status' => Payment::STATUS_FAILED,
                    'gateway_response' => $normalized,
                ]);
                $this->telemetry->increment('payments_failed_total');
                $this->alertService->alertPaymentFailure([
                    'gateway' => $gateway->name(),
                    'subscription_id' => $subscription->id,
                    'payment_id' => $payment->id,
                ]);
            }

            $this->logFinancialEvent('payment.webhook.processed', $subscription->user, $subscription, [
                'gateway' => $gateway->name(),
                'event_type' => $eventType,
                'gateway_payment_id' => $normalized['payment_id'] ?? null,
                'status_before' => $statusBefore,
                'status_after' => $statusAfter,
            ]);
            $this->writeSubscriptionAudit($subscription, 'webhook_'.$eventType, [
                'gateway' => $gateway->name(),
                'event_id' => $eventId,
                'event_type' => $eventType,
                'status_before' => $statusBefore,
                'status_after' => $statusAfter,
                'payload' => $normalized,
            ]);
        });
    }

    /**
     * Atribui o plano Free ao usuário (para novos cadastros).
     */
    public function assignFreePlan(User $user): UserSubscription
    {
        $free = Plan::free();

        if (! $free) {
            throw new PlanConfigurationException('Plano Free não encontrado. Execute o seeder.');
        }

        return $this->subscribe($user, $free);
    }

    /**
     * Verifica se o usuário pode usar determinada feature.
     */
    public function canUse(User $user, string $feature): bool
    {
        return $user->canUseFeature($feature);
    }

    /**
     * Retorna o limite numérico de uma feature (null = ilimitado).
     */
    public function getLimit(User $user, string $feature): ?int
    {
        return $user->featureLimit($feature);
    }

    /**
     * Verifica se o usuário atingiu o limite de uma feature.
     */
    public function hasReachedLimit(User $user, string $feature, int $currentUsage): bool
    {
        $limit = $this->getLimit($user, $feature);

        if ($limit === null) {
            return false; // ilimitado
        }

        return $currentUsage >= $limit;
    }

    // ─── Private ─────────────────────────────────────────────

    private function calculateExpiry(Plan $plan): ?\DateTimeInterface
    {
        return match ($plan->billing_cycle) {
            'monthly' => now()->addMonth(),
            'yearly' => now()->addYear(),
            'lifetime' => null,
            default => now()->addMonth(),
        };
    }

    private function determineAction(?Plan $current, Plan $new): string
    {
        if (! $current) {
            return 'subscribe';
        }

        return $new->price > $current->price ? 'upgrade' : 'downgrade';
    }

    private function processPayment(
        User $user,
        UserSubscription $subscription,
        Plan $plan,
        PaymentGatewayInterface $gateway,
    ): Payment {
        $result = $this->runGatewayCall(fn () => $gateway->charge($user, $plan), $gateway->name(), 'charge');

        return Payment::create([
            'user_id' => $user->id,
            'user_subscription_id' => $subscription->id,
            'gateway' => $gateway->name(),
            'gateway_payment_id' => $result['id'] ?? null,
            'amount' => $plan->price,
            'currency' => 'BRL',
            'status' => Payment::STATUS_PENDING,
            'gateway_response' => $result,
            'paid_at' => null,
        ]);
    }

    private function resolveSubscriptionStatus(bool $hasTrial, bool $requiresPayment): string
    {
        if ($hasTrial) {
            return UserSubscription::STATUS_TRIALING;
        }

        if ($requiresPayment) {
            return UserSubscription::STATUS_PENDING;
        }

        return UserSubscription::STATUS_ACTIVE;
    }

    private function assertGatewayForPaidPlan(bool $requiresPayment, ?PaymentGatewayInterface $gateway, string $message): void
    {
        if ($requiresPayment && ! $gateway) {
            throw new InvalidArgumentException($message);
        }
    }

    private function registerProcessedEvent(string $gateway, string $eventId, string $eventType, array $payload): bool
    {
        try {
            ProcessedEvent::create([
                'gateway' => $gateway,
                'event_id' => $eventId,
                'event_type' => $eventType,
                'payload' => $payload,
                'processed_at' => now(),
            ]);

            return true;
        } catch (QueryException $e) {
            if (($e->errorInfo[0] ?? null) === '23000') {
                return false;
            }

            throw $e;
        }
    }
}
