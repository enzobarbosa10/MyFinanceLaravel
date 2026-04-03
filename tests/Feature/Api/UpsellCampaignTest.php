<?php

namespace Tests\Feature\Api;

use App\Models\FeatureUsage;
use App\Models\Notification;
use App\Models\Plan;
use App\Models\PlanFeature;
use App\Models\User;
use App\Models\UserSubscription;
use App\Services\UpsellRecommendationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpsellCampaignTest extends TestCase
{
    use RefreshDatabase;

    public function test_automatic_upsell_campaign_notifies_near_limit_users(): void
    {
        $user = User::factory()->create();
        $plan = Plan::create([
            'slug' => 'pro',
            'name' => 'Pro',
            'price' => 19.90,
            'billing_cycle' => 'monthly',
            'trial_days' => 0,
            'is_active' => true,
        ]);

        PlanFeature::create([
            'plan_id' => $plan->id,
            'feature' => 'transactions_per_month',
            'limit_value' => 100,
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
            'feature' => 'transactions_per_month',
            'period' => now()->format('Y-m'),
            'usage_count' => 90,
        ]);

        $result = app(UpsellRecommendationService::class)->runAutomaticUpsellCampaign();

        $this->assertSame(1, $result['sent']);
        $this->assertDatabaseHas('notifications', [
            'user_id' => $user->id,
            'type' => 'upsell_suggestion',
        ]);
    }
}
