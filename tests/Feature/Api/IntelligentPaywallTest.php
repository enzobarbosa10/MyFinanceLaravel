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

class IntelligentPaywallTest extends TestCase
{
    use RefreshDatabase;

    private const PAYWALL_TEST_ENDPOINT = '/api/test/paywall-inteligente';

    protected function setUp(): void
    {
        parent::setUp();

        Route::middleware(['auth:sanctum', 'intelligent.paywall:ai_assistant'])
            ->get(self::PAYWALL_TEST_ENDPOINT, fn () => response()->json(['ok' => true]));
    }

    public function test_paywall_suggests_upgrade_when_user_reaches_80_percent_of_limit(): void
    {
        $user = User::factory()->create();
        $plan = Plan::create([
            'slug' => 'pro',
            'name' => 'Pro',
            'price' => 29.90,
            'billing_cycle' => 'monthly',
            'trial_days' => 0,
            'is_active' => true,
        ]);

        PlanFeature::create([
            'plan_id' => $plan->id,
            'feature' => 'ai_assistant',
            'limit_value' => 10,
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
            'usage_count' => 8,
        ]);

        Sanctum::actingAs($user);

        $response = $this->getJson(self::PAYWALL_TEST_ENDPOINT)->assertOk();
        $this->assertNotEmpty($response->headers->get('X-Upgrade-Suggestion'));
    }

    public function test_paywall_blocks_when_limit_is_reached(): void
    {
        $user = User::factory()->create();
        $plan = Plan::create([
            'slug' => 'pro',
            'name' => 'Pro',
            'price' => 29.90,
            'billing_cycle' => 'monthly',
            'trial_days' => 0,
            'is_active' => true,
        ]);

        PlanFeature::create([
            'plan_id' => $plan->id,
            'feature' => 'ai_assistant',
            'limit_value' => 10,
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
            'usage_count' => 10,
        ]);

        Sanctum::actingAs($user);

        $this->getJson(self::PAYWALL_TEST_ENDPOINT)
            ->assertStatus(429)
            ->assertJsonPath('errors.0.details.upgrade.recommended_plan', 'premium');
    }
}
