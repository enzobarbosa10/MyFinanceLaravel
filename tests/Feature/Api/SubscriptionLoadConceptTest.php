<?php

namespace Tests\Feature\Api;

use App\Models\Plan;
use App\Models\User;
use App\Models\UserSubscription;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class SubscriptionLoadConceptTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware(ThrottleRequests::class);
    }

    public function test_repeated_idempotent_subscription_requests_under_load_keep_single_open_subscription(): void
    {
        $user = User::factory()->create();
        $plan = Plan::create([
            'slug' => 'free',
            'name' => 'Free',
            'price' => 0,
            'billing_cycle' => 'monthly',
            'trial_days' => 0,
            'is_active' => true,
        ]);

        Sanctum::actingAs($user);

        for ($attempt = 0; $attempt < 20; $attempt++) {
            $this->postJson('/api/v1/plans/subscribe', [
                'plan' => $plan->slug,
            ], [
                'Idempotency-Key' => 'load-sub-001',
            ])->assertCreated();
        }

        $this->assertSame(1, UserSubscription::query()->where('user_id', $user->id)->count());
    }
}
