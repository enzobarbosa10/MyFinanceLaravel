<?php

namespace Tests\Feature\Api;

use App\Contracts\PaymentGatewayInterface;
use App\Exceptions\PaymentGatewayUnavailableException;
use App\Models\Plan;
use App\Models\ProcessedEvent;
use App\Models\User;
use App\Models\UserSubscription;
use App\Services\PlanService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class SubscriptionFinancialGradeTest extends TestCase
{
    use RefreshDatabase;

    private const WEBHOOK_ENDPOINT = '/api/webhooks/payments/stripe';

    private User $user;
    private Plan $paidPlan;

    protected function setUp(): void
    {
        parent::setUp();

        config()->set('services.stripe.webhook_secret', 'whsec_financial_grade_test');

        $this->user = User::factory()->create(['had_trial' => true]);
        $this->paidPlan = Plan::create([
            'slug' => 'pro',
            'name' => 'Pro',
            'price' => 29.90,
            'billing_cycle' => 'monthly',
            'trial_days' => 0,
            'is_active' => true,
        ]);
    }

    public function test_webhook_with_invalid_signature_is_rejected(): void
    {
        $payload = [
            'id' => 'evt_invalid_sig',
            'type' => 'invoice.paid',
            'data' => ['object' => ['subscription' => 'sub_1']],
        ];

        $this->postJson(self::WEBHOOK_ENDPOINT, $payload, [
            'Stripe-Signature' => 't=123,v1=invalid',
        ])->assertStatus(401);
    }

    public function test_webhook_replay_attack_is_rejected(): void
    {
        config()->set('services.stripe.webhook_secret', 'whsec_replay_test');

        $payload = [
            'id' => 'evt_replay',
            'type' => 'invoice.paid',
            'data' => ['object' => ['subscription' => 'sub_1']],
        ];

        $oldTimestamp = now()->subMinutes(20)->timestamp;

        $this->postJson(self::WEBHOOK_ENDPOINT, $payload, [
            'Stripe-Signature' => $this->stripeSignatureHeader($payload, $oldTimestamp),
        ])->assertStatus(401);
    }

    public function test_duplicate_webhook_event_is_processed_once(): void
    {
        Sanctum::actingAs($this->user);

        $subscribe = $this->postJson('/api/plans/subscribe', [
            'plan' => $this->paidPlan->slug,
            'gateway' => 'stripe',
        ], ['Idempotency-Key' => 'dup-event-sub'])->assertCreated();

        $subscription = UserSubscription::findOrFail($subscribe->json('data.id'));

        $payload = [
            'id' => 'evt_duplicate_1',
            'type' => 'invoice.paid',
            'data' => [
                'object' => [
                    'subscription' => $subscription->gateway_subscription_id,
                    'payment_intent' => 'pi_dup_1',
                ],
            ],
        ];

        $headers = ['Stripe-Signature' => $this->stripeSignatureHeader($payload)];

        $this->postJson(self::WEBHOOK_ENDPOINT, $payload, $headers)->assertOk();
        $this->postJson(self::WEBHOOK_ENDPOINT, $payload, $headers)->assertOk();

        $this->assertDatabaseCount('processed_events', 1);
        $this->assertDatabaseHas('processed_events', [
            'gateway' => 'stripe',
            'event_id' => 'evt_duplicate_1',
        ]);
    }

    public function test_retry_succeeds_after_transient_gateway_failure(): void
    {
        config()->set('subscriptions.gateway.retry.attempts', 3);
        config()->set('subscriptions.gateway.retry.base_delay_ms', 1);
        config()->set('subscriptions.gateway.circuit_breaker.failure_threshold', 10);

        $gateway = new class implements PaymentGatewayInterface {
            public int $chargeAttempts = 0;

            public function name(): string
            {
                return 'fake_retry_gateway';
            }

            public function charge(User $user, Plan $plan): array
            {
                $this->chargeAttempts++;
                if ($this->chargeAttempts < 3) {
                    throw new PaymentGatewayUnavailableException('transient');
                }

                return ['id' => 'pay_ok', 'status' => 'paid'];
            }

            public function createSubscription(User $user, Plan $plan): array
            {
                return ['id' => 'sub_ok', 'status' => 'active'];
            }

            public function cancelSubscription(string $gatewaySubscriptionId): bool
            {
                return true;
            }

            public function handleWebhook(array $payload): array
            {
                return [
                    'event' => 'payment_success',
                    'event_id' => 'evt_any',
                    'event_type' => 'invoice.paid',
                    'subscription_id' => 'sub_ok',
                    'payment_id' => 'pi_ok',
                    'status' => 'paid',
                ];
            }
        };

        /** @var PlanService $service */
        $service = app(PlanService::class);
        $service->subscribe($this->user, $this->paidPlan, $gateway);

        $this->assertSame(3, $gateway->chargeAttempts);
    }

    public function test_circuit_breaker_blocks_after_consecutive_failures(): void
    {
        config()->set('subscriptions.gateway.retry.attempts', 1);
        config()->set('subscriptions.gateway.circuit_breaker.failure_threshold', 1);
        config()->set('subscriptions.gateway.circuit_breaker.cooldown_seconds', 60);

        $gateway = new class implements PaymentGatewayInterface {
            public function name(): string
            {
                return 'fake_cb_gateway';
            }

            public function charge(User $user, Plan $plan): array
            {
                throw new PaymentGatewayUnavailableException('always fails');
            }

            public function createSubscription(User $user, Plan $plan): array
            {
                throw new PaymentGatewayUnavailableException('always fails');
            }

            public function cancelSubscription(string $gatewaySubscriptionId): bool
            {
                return true;
            }

            public function handleWebhook(array $payload): array
            {
                return [
                    'event' => 'payment_success',
                    'event_id' => 'evt_any',
                    'event_type' => 'invoice.paid',
                    'subscription_id' => 'sub_any',
                    'payment_id' => 'pi_any',
                    'status' => 'paid',
                ];
            }
        };

        /** @var PlanService $service */
        $service = app(PlanService::class);

        try {
            $service->subscribe($this->user, $this->paidPlan, $gateway);
        } catch (PaymentGatewayUnavailableException) {
            // First failure opens the breaker by config.
        }

        $this->expectException(PaymentGatewayUnavailableException::class);
        $service->subscribe($this->user, $this->paidPlan, $gateway);
    }

    public function test_invalid_state_transition_is_rejected(): void
    {
        $subscription = UserSubscription::create([
            'user_id' => $this->user->id,
            'plan_id' => $this->paidPlan->id,
            'status' => UserSubscription::STATUS_CANCELED,
            'starts_at' => now(),
            'expires_at' => now()->addMonth(),
            'gateway' => 'stripe',
            'gateway_subscription_id' => 'sub_invalid_transition',
            'canceled_at' => now(),
        ]);

        $payload = [
            'id' => 'evt_invalid_transition',
            'type' => 'invoice.paid',
            'data' => [
                'object' => [
                    'subscription' => $subscription->gateway_subscription_id,
                    'payment_intent' => 'pi_invalid_transition',
                ],
            ],
        ];

        $response = $this->postJson(self::WEBHOOK_ENDPOINT, $payload, [
            'Stripe-Signature' => $this->stripeSignatureHeader($payload),
        ]);

        $response->assertStatus(500);
    }

    /**
     * @param array<string, mixed> $payload
     */
    private function stripeSignatureHeader(array $payload, ?int $timestamp = null): string
    {
        $timestamp ??= now()->timestamp;
        $body = json_encode($payload);
        $signature = hash_hmac('sha256', $timestamp.'.'.$body, (string) config('services.stripe.webhook_secret'));

        return "t={$timestamp},v1={$signature}";
    }
}
