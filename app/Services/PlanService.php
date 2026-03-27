<?php

namespace App\Services;

use App\Contracts\PaymentGatewayInterface;
use App\Models\Payment;
use App\Models\Plan;
use App\Models\User;
use App\Models\UserSubscription;
use Illuminate\Support\Facades\DB;

class PlanService
{
    /**
     * Inscreve o usuário em um plano.
     */
    public function subscribe(User $user, Plan $plan, ?PaymentGatewayInterface $gateway = null): UserSubscription
    {
        return DB::transaction(function () use ($user, $plan, $gateway) {
            // Cancela assinatura ativa anterior
            $this->cancelCurrentSubscription($user, 'upgrade');

            $isFree = $plan->price <= 0;
            $hasTrial = $plan->trial_days > 0 && ! $this->hadTrialBefore($user);

            $subscription = UserSubscription::create([
                'user_id' => $user->id,
                'plan_id' => $plan->id,
                'status' => $hasTrial ? 'trialing' : 'active',
                'trial_ends_at' => $hasTrial ? now()->addDays($plan->trial_days) : null,
                'starts_at' => now(),
                'expires_at' => $this->calculateExpiry($plan),
                'gateway' => $gateway?->name(),
                'gateway_subscription_id' => null,
            ]);

            // Registra no histórico
            DB::table('subscription_history')->insert([
                'user_id' => $user->id,
                'from_plan_id' => null,
                'to_plan_id' => $plan->id,
                'action' => 'subscribe',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Processa pagamento se plano pago e não é trial
            if (! $isFree && ! $hasTrial && $gateway) {
                $this->processPayment($user, $subscription, $plan, $gateway);
            }

            return $subscription;
        });
    }

    /**
     * Upgrade/downgrade de plano.
     */
    public function changePlan(User $user, Plan $newPlan, ?PaymentGatewayInterface $gateway = null): UserSubscription
    {
        $currentPlan = $user->currentPlan();
        $action = $this->determineAction($currentPlan, $newPlan);

        return DB::transaction(function () use ($user, $newPlan, $gateway, $currentPlan, $action) {
            $this->cancelCurrentSubscription($user, $action);

            $subscription = UserSubscription::create([
                'user_id' => $user->id,
                'plan_id' => $newPlan->id,
                'status' => 'active',
                'starts_at' => now(),
                'expires_at' => $this->calculateExpiry($newPlan),
                'gateway' => $gateway?->name(),
            ]);

            DB::table('subscription_history')->insert([
                'user_id' => $user->id,
                'from_plan_id' => $currentPlan?->id,
                'to_plan_id' => $newPlan->id,
                'action' => $action,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            if ($newPlan->price > 0 && $gateway) {
                $this->processPayment($user, $subscription, $newPlan, $gateway);
            }

            return $subscription;
        });
    }

    /**
     * Cancela a assinatura ativa do usuário.
     */
    public function cancel(User $user): bool
    {
        $sub = $user->userSubscription;
        if (! $sub) {
            return false;
        }

        $sub->update([
            'status' => 'canceled',
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

        return true;
    }

    /**
     * Atribui o plano Free ao usuário (para novos cadastros).
     */
    public function assignFreePlan(User $user): UserSubscription
    {
        $free = Plan::free();

        if (! $free) {
            throw new \RuntimeException('Plano Free não encontrado. Execute o seeder.');
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

    private function cancelCurrentSubscription(User $user, string $action): void
    {
        $sub = $user->userSubscription;
        if ($sub && $sub->isActive()) {
            $sub->update([
                'status' => 'canceled',
                'canceled_at' => now(),
            ]);
        }
    }

    private function hadTrialBefore(User $user): bool
    {
        return UserSubscription::where('user_id', $user->id)
            ->where('status', 'trialing')
            ->exists();
    }

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
        $result = $gateway->charge($user, $plan);

        return Payment::create([
            'user_id' => $user->id,
            'user_subscription_id' => $subscription->id,
            'gateway' => $gateway->name(),
            'gateway_payment_id' => $result['id'] ?? null,
            'amount' => $plan->price,
            'currency' => 'BRL',
            'status' => $result['status'] ?? 'pending',
            'gateway_response' => $result,
            'paid_at' => ($result['status'] ?? '') === 'paid' ? now() : null,
        ]);
    }
}
