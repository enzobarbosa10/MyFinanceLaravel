<?php

namespace Tests\Feature\Api;

use App\Models\FeatureUsage;
use App\Models\Plan;
use App\Models\PlanFeature;
use App\Models\User;
use App\Models\UserSubscription;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class FeatureUsageLimitTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Route::middleware(['auth:sanctum', 'usage.limit:ai_assistant'])
            ->get('/api/test/usage-limit', fn () => response()->json(['ok' => true]));
    }

    public function test_usage_limit_blocks_request_when_limit_is_reached(): void
    {
        $user = User::factory()->create();
        $plan = Plan::create([
            'slug' => 'basic',
            'name' => 'Basic',
            'price' => 9.90,
            'billing_cycle' => 'monthly',
            'trial_days' => 0,
            'is_active' => true,
        ]);

        PlanFeature::create([
            'plan_id' => $plan->id,
            'feature' => 'ai_assistant',
            'limit_value' => 2,
        ]);

        UserSubscription::create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'status' => UserSubscription::STATUS_ACTIVE,
            'starts_at' => now(),
            'expires_at' => now()->addMonth(),
        ]);

        FeatureUsage::create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'feature' => 'ai_assistant',
            'period' => now()->format('Y-m'),
            'usage_count' => 2,
        ]);

        Sanctum::actingAs($user);

        $this->getJson('/api/test/usage-limit')
            ->assertStatus(429)
            ->assertJsonPath('errors.0.details.limit', 2);
    }

    public function test_v1_subscription_route_remains_available(): void
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

        $this->postJson('/api/v1/plans/subscribe', [
            'plan' => $plan->slug,
        ], [
            'Idempotency-Key' => 'v1-subscribe-001',
        ])->assertCreated();
    }
}
