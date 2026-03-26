<?php

namespace Tests\Feature;

use App\Enums\GoalStatus;
use App\Models\Goal;
use App\Models\Plan;
use App\Models\User;
use App\Models\UserSubscription;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GoalTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Goal $goal;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();

        // Create an active subscription so the user passes the subscription middleware
        $plan = Plan::create([
            'slug' => 'premium',
            'name' => 'Premium',
            'price' => 19.90,
            'billing_cycle' => 'monthly',
        ]);
        UserSubscription::create([
            'user_id' => $this->user->id,
            'plan_id' => $plan->id,
            'status' => 'active',
            'starts_at' => now(),
            'expires_at' => now()->addMonth(),
        ]);

        $this->goal = Goal::create([
            'user_id' => $this->user->id,
            'name' => 'Viagem Europa',
            'target_amount' => 5000.00,
            'current_amount' => 0.00,
            'deadline' => now()->addYear()->toDateString(),
            'icon' => '✈️',
            'status' => GoalStatus::Active,
        ]);
    }

    public function test_contributing_increments_current_amount(): void
    {
        $response = $this->actingAs($this->user)->post(route('goals.contribute', $this->goal), [
            'amount' => 1000.00,
            'notes' => 'Primeira contribuição',
        ]);

        $response->assertRedirect(route('goals.show', $this->goal));

        $goal = $this->goal->fresh();
        $this->assertEquals(1000.00, $goal->current_amount);
        $this->assertEquals(GoalStatus::Active, $goal->status);

        $this->assertDatabaseHas('goal_contributions', [
            'goal_id' => $this->goal->id,
            'amount' => 1000.00,
        ]);
    }

    public function test_reaching_target_amount_marks_goal_as_completed(): void
    {
        // Contribute the full target amount
        $response = $this->actingAs($this->user)->post(route('goals.contribute', $this->goal), [
            'amount' => 5000.00,
        ]);

        $response->assertRedirect(route('goals.show', $this->goal));

        $goal = $this->goal->fresh();
        $this->assertEquals(5000.00, $goal->current_amount);
        $this->assertEquals(GoalStatus::Completed, $goal->status);
    }

    public function test_exceeding_target_amount_also_marks_goal_as_completed(): void
    {
        $response = $this->actingAs($this->user)->post(route('goals.contribute', $this->goal), [
            'amount' => 6000.00,
        ]);

        $response->assertRedirect(route('goals.show', $this->goal));

        $goal = $this->goal->fresh();
        $this->assertEquals(6000.00, $goal->current_amount);
        $this->assertEquals(GoalStatus::Completed, $goal->status);
    }

    public function test_user_cannot_contribute_to_another_users_goal(): void
    {
        $otherUser = User::factory()->create();
        // Give them a subscription so they pass the middleware
        UserSubscription::create([
            'user_id' => $otherUser->id,
            'plan_id' => Plan::where('slug', 'premium')->first()->id,
            'status' => 'active',
            'starts_at' => now(),
            'expires_at' => now()->addMonth(),
        ]);

        $response = $this->actingAs($otherUser)->post(route('goals.contribute', $this->goal), [
            'amount' => 500.00,
        ]);

        $response->assertForbidden();

        // Goal remains unchanged
        $this->assertEquals(0.00, $this->goal->fresh()->current_amount);
    }

    public function test_multiple_contributions_accumulate_correctly(): void
    {
        $this->actingAs($this->user)->post(route('goals.contribute', $this->goal), [
            'amount' => 1500.00,
        ]);

        $this->actingAs($this->user)->post(route('goals.contribute', $this->goal), [
            'amount' => 2000.00,
        ]);

        $goal = $this->goal->fresh();
        $this->assertEquals(3500.00, $goal->current_amount);
        $this->assertEquals(GoalStatus::Active, $goal->status);
        $this->assertCount(2, $goal->contributions);
    }
}
