<?php

namespace Tests\Feature\Api;

use App\Models\Payment;
use App\Models\Plan;
use App\Models\User;
use App\Models\UserSubscription;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class SubscriptionApiTest extends TestCase
{
    use RefreshDatabase;

    private const SUBSCRIBE_ENDPOINT = '/api/plans/subscribe';
    private const CHANGE_ENDPOINT = '/api/plans/change';
    private const CANCEL_ENDPOINT = '/api/plans/cancel';
    private const STRIPE_WEBHOOK_ENDPOINT = '/api/webhooks/payments/stripe';

    private User $user;
    private Plan $freePlan;
    private Plan $proPlan;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();

        $this->freePlan = Plan::create([
            'slug' => 'free',
            'name' => 'Free',
            'price' => 0,
            'billing_cycle' => 'monthly',
            'trial_days' => 0,
            'is_active' => true,
        ]);

        $this->proPlan = Plan::create([
            'slug' => 'pro',
            'name' => 'Pro',
            'price' => 29.90,
            'billing_cycle' => 'monthly',
            'trial_days' => 7,
            'is_active' => true,
        ]);
    }

    public function test_can_create_subscription_for_free_plan(): void
    {
        Sanctum::actingAs($this->user);

        $response = $this->postJson(self::SUBSCRIBE_ENDPOINT, [
            'plan' => $this->freePlan->slug,
        ], [
            'Idempotency-Key' => 'sub-free-001',
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.status', UserSubscription::STATUS_ACTIVE)
            ->assertJsonPath('errors', null);

        $this->assertDatabaseHas('user_subscriptions', [
            'user_id' => $this->user->id,
            'plan_id' => $this->freePlan->id,
            'status' => UserSubscription::STATUS_ACTIVE,
        ]);
    }

    public function test_upgrade_to_paid_plan_requires_gateway_when_no_trial(): void
    {
        Sanctum::actingAs($this->user);
        $this->user->forceFill(['had_trial' => true])->save();

        $response = $this->postJson(self::SUBSCRIBE_ENDPOINT, [
            'plan' => $this->proPlan->slug,
        ], [
            'Idempotency-Key' => 'sub-paid-no-gateway',
        ]);

        $response->assertStatus(422);
        $this->assertStringContainsString('Gateway', $response->json('errors.0.message'));
    }

    public function test_idempotency_key_prevents_duplicate_subscription_creation(): void
    {
        Sanctum::actingAs($this->user);

        $payload = [
            'plan' => $this->freePlan->slug,
        ];
        $headers = ['Idempotency-Key' => 'same-key-subscription'];

        $first = $this->postJson(self::SUBSCRIBE_ENDPOINT, $payload, $headers);
        $second = $this->postJson(self::SUBSCRIBE_ENDPOINT, $payload, $headers);

        $first->assertCreated();
        $second->assertCreated();

        $this->assertEquals($first->json('data.id'), $second->json('data.id'));

        $this->assertEquals(
            1,
            UserSubscription::where('user_id', $this->user->id)->count()
        );
    }

    public function test_trial_is_granted_only_once_per_user(): void
    {
        Sanctum::actingAs($this->user);

        $first = $this->postJson(self::SUBSCRIBE_ENDPOINT, [
            'plan' => $this->proPlan->slug,
        ], [
            'Idempotency-Key' => 'trial-first',
        ]);

        $first->assertCreated()->assertJsonPath('data.status', UserSubscription::STATUS_TRIALING);
        $this->assertTrue((bool) $this->user->fresh()->had_trial);

        $this->postJson(self::CANCEL_ENDPOINT, [], ['Idempotency-Key' => 'cancel-first'])
            ->assertOk();

        $second = $this->postJson(self::SUBSCRIBE_ENDPOINT, [
            'plan' => $this->proPlan->slug,
            'gateway' => 'stripe',
        ], [
            'Idempotency-Key' => 'trial-second',
        ]);

        $second->assertCreated()->assertJsonPath('data.status', UserSubscription::STATUS_PENDING);
    }

    public function test_can_confirm_payment_via_webhook(): void
    {
        Sanctum::actingAs($this->user);
        $this->user->forceFill(['had_trial' => true])->save();

        $subscribe = $this->postJson(self::SUBSCRIBE_ENDPOINT, [
            'plan' => $this->proPlan->slug,
            'gateway' => 'stripe',
        ], [
            'Idempotency-Key' => 'sub-webhook-001',
        ]);

        $subscribe->assertCreated()->assertJsonPath('data.status', UserSubscription::STATUS_PENDING);

        $subscriptionId = $subscribe->json('data.id');
        $subscription = UserSubscription::findOrFail($subscriptionId);

        $this->assertNotNull($subscription->gateway_subscription_id);
        $this->assertDatabaseHas('payments', [
            'user_subscription_id' => $subscription->id,
            'status' => Payment::STATUS_PENDING,
        ]);

        $webhookPayload = [
            'id' => 'evt_test_paid_1',
            'type' => 'invoice.paid',
            'data' => [
                'object' => [
                    'subscription' => $subscription->gateway_subscription_id,
                    'payment_intent' => 'pi_test_123',
                ],
            ],
        ];

        $signatureHeader = $this->stripeSignatureHeader($webhookPayload);

        $this->postJson(self::STRIPE_WEBHOOK_ENDPOINT, $webhookPayload, [
            'Stripe-Signature' => $signatureHeader,
        ])
            ->assertOk()
            ->assertJsonPath('data.received', true);

        $this->assertDatabaseHas('user_subscriptions', [
            'id' => $subscription->id,
            'status' => UserSubscription::STATUS_ACTIVE,
        ]);

        $this->assertDatabaseHas('payments', [
            'user_subscription_id' => $subscription->id,
            'status' => Payment::STATUS_PAID,
        ]);
    }

    public function test_change_plan_supports_upgrade_and_downgrade(): void
    {
        Sanctum::actingAs($this->user);

        $premium = Plan::create([
            'slug' => 'premium',
            'name' => 'Premium',
            'price' => 49.90,
            'billing_cycle' => 'monthly',
            'trial_days' => 0,
            'is_active' => true,
        ]);

        $this->postJson(self::SUBSCRIBE_ENDPOINT, [
            'plan' => $this->freePlan->slug,
        ], [
            'Idempotency-Key' => 'change-seed-sub',
        ])->assertCreated();

        $upgrade = $this->putJson(self::CHANGE_ENDPOINT, [
            'plan' => $premium->slug,
            'gateway' => 'stripe',
        ], [
            'Idempotency-Key' => 'change-upgrade-001',
        ]);

        $upgrade->assertOk()->assertJsonPath('data.status', UserSubscription::STATUS_PENDING);

        $downgrade = $this->putJson(self::CHANGE_ENDPOINT, [
            'plan' => $this->freePlan->slug,
        ], [
            'Idempotency-Key' => 'change-downgrade-001',
        ]);

        $downgrade->assertOk()->assertJsonPath('data.status', UserSubscription::STATUS_ACTIVE);
    }

    public function test_database_constraint_blocks_multiple_open_subscriptions_simulated(): void
    {
        $this->expectException(QueryException::class);

        UserSubscription::create([
            'user_id' => $this->user->id,
            'plan_id' => $this->freePlan->id,
            'status' => UserSubscription::STATUS_ACTIVE,
            'starts_at' => now(),
            'expires_at' => now()->addMonth(),
        ]);

        UserSubscription::create([
            'user_id' => $this->user->id,
            'plan_id' => $this->proPlan->id,
            'status' => UserSubscription::STATUS_PENDING,
            'starts_at' => now(),
            'expires_at' => now()->addMonth(),
        ]);
    }

    /**
     * @param array<string, mixed> $payload
     */
    private function stripeSignatureHeader(array $payload, ?int $timestamp = null): string
    {
        config()->set('services.stripe.webhook_secret', 'whsec_test_subscription');

        $timestamp ??= now()->timestamp;
        $body = json_encode($payload);
        $signature = hash_hmac('sha256', $timestamp.'.'.$body, (string) config('services.stripe.webhook_secret'));

        return "t={$timestamp},v1={$signature}";
    }
}
