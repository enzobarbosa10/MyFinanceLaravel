<?php

namespace Tests\Feature\Api;

use App\Models\FeatureUsage;
use App\Models\Plan;
use App\Models\User;
use App\Models\UserSubscription;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ProductInsightsApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_fetch_product_insights(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $plan = Plan::create([
            'slug' => 'pro',
            'name' => 'Pro',
            'price' => 19.90,
            'billing_cycle' => 'monthly',
            'trial_days' => 0,
            'is_active' => true,
        ]);

        UserSubscription::create([
            'user_id' => $admin->id,
            'plan_id' => $plan->id,
            'status' => UserSubscription::STATUS_ACTIVE,
            'starts_at' => now(),
            'expires_at' => now()->addMonth(),
        ]);

        FeatureUsage::create([
            'user_id' => $admin->id,
            'plan_id' => $plan->id,
            'feature' => 'ai_assistant',
            'period' => now()->format('Y-m'),
            'usage_count' => 25,
        ]);

        Sanctum::actingAs($admin);

        $this->getJson('/api/v1/admin/product/insights')
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    'segments',
                    'avg_usage_by_plan',
                    'upgrade_rate',
                    'churn_rate_by_plan',
                    'generated_at',
                ],
            ]);
    }

    public function test_non_admin_cannot_fetch_product_insights(): void
    {
        $user = User::factory()->create(['is_admin' => false]);
        Sanctum::actingAs($user);

        $this->getJson('/api/admin/product/insights')
            ->assertStatus(403);
    }
}
