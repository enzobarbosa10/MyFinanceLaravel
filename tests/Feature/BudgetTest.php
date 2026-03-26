<?php

namespace Tests\Feature;

use App\Enums\TransactionType;
use App\Models\Budget;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BudgetTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();

        $this->category = Category::create([
            'user_id' => $this->user->id,
            'name' => 'Alimentação',
            'type' => TransactionType::Saida->value,
        ]);
    }

    public function test_creating_budget_stores_correctly(): void
    {
        $month = date('Y-m');

        $response = $this->actingAs($this->user)->post(route('budgets.store'), [
            'category_id' => $this->category->id,
            'amount' => 800.00,
            'month' => $month,
        ]);

        $response->assertRedirect(route('budgets.index'));

        $this->assertDatabaseHas('budgets', [
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'amount' => 800.00,
            'month' => $month,
        ]);
    }

    public function test_update_or_create_does_not_duplicate_same_category_and_month(): void
    {
        $month = date('Y-m');

        // First budget creation
        $this->actingAs($this->user)->post(route('budgets.store'), [
            'category_id' => $this->category->id,
            'amount' => 800.00,
            'month' => $month,
        ]);

        // Second call with same category and month — should UPDATE, not create new
        $this->actingAs($this->user)->post(route('budgets.store'), [
            'category_id' => $this->category->id,
            'amount' => 1200.00,
            'month' => $month,
        ]);

        $budgets = Budget::where('user_id', $this->user->id)
            ->where('category_id', $this->category->id)
            ->where('month', $month)
            ->get();

        $this->assertCount(1, $budgets);
        $this->assertEquals(1200.00, $budgets->first()->amount);
    }

    public function test_different_months_create_separate_budgets(): void
    {
        $this->actingAs($this->user)->post(route('budgets.store'), [
            'category_id' => $this->category->id,
            'amount' => 800.00,
            'month' => '2026-01',
        ]);

        $this->actingAs($this->user)->post(route('budgets.store'), [
            'category_id' => $this->category->id,
            'amount' => 900.00,
            'month' => '2026-02',
        ]);

        $budgets = Budget::where('user_id', $this->user->id)
            ->where('category_id', $this->category->id)
            ->get();

        $this->assertCount(2, $budgets);
    }

    public function test_different_categories_create_separate_budgets(): void
    {
        $month = date('Y-m');

        $otherCategory = Category::create([
            'user_id' => $this->user->id,
            'name' => 'Transporte',
            'type' => TransactionType::Saida->value,
        ]);

        $this->actingAs($this->user)->post(route('budgets.store'), [
            'category_id' => $this->category->id,
            'amount' => 800.00,
            'month' => $month,
        ]);

        $this->actingAs($this->user)->post(route('budgets.store'), [
            'category_id' => $otherCategory->id,
            'amount' => 400.00,
            'month' => $month,
        ]);

        $budgets = Budget::where('user_id', $this->user->id)
            ->where('month', $month)
            ->get();

        $this->assertCount(2, $budgets);
    }
}
