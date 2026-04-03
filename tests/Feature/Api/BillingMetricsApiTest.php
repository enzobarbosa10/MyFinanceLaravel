<?php

namespace Tests\Feature\Api;

use App\Models\Plan;
use App\Models\User;
use App\Models\UserSubscription;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class BillingMetricsApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_fetch_billing_metrics_on_v1_route(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $plan = Plan::create([
            'slug' => 'pro',
            'name' => 'Pro',
            'price' => 30.00,
            'billing_cycle' => 'monthly',
            'trial_days' => 0,
            'is_active' => true,
        ]);

        UserSubscription::create([
            'user_id' => $admin->id,
            'plan_id' => $plan->id,
            'status' => UserSubscription::STATUS_ACTIVE,
            'starts_at' => now()->subMonth(),
            'expires_at' => now()->addMonth(),
        ]);

        Sanctum::actingAs($admin);

        $this->getJson('/api/v1/admin/billing/metrics')
            ->assertOk()
            ->assertJsonPath('data.mrr', 30)
            ->assertJsonPath('data.arr', 360);
    }

    public function test_non_admin_cannot_fetch_billing_metrics(): void
    {
        $user = User::factory()->create(['is_admin' => false]);
        Sanctum::actingAs($user);

        $this->getJson('/api/admin/billing/metrics')
            ->assertStatus(403);
    }
}
