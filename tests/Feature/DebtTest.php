<?php

namespace Tests\Feature;

use App\Models\Debt;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DebtTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Debt $debt;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();

        $this->debt = Debt::create([
            'user_id' => $this->user->id,
            'name' => 'Empréstimo Pessoal',
            'total_amount' => 10000.00,
            'paid_amount' => 0.00,
            'monthly_interest_rate' => 1.50,
            'min_payment' => 500.00,
            'due_day' => 15,
            'creditor' => 'Banco XYZ',
            'status' => 'active',
        ]);
    }

    public function test_payment_increments_paid_amount(): void
    {
        $response = $this->actingAs($this->user)->post(route('debts.pay'), [
            'debt_id' => $this->debt->id,
            'amount' => 2000.00,
            'notes' => 'Parcela janeiro',
        ]);

        $response->assertRedirect(route('debts.index'));

        $debt = $this->debt->fresh();
        $this->assertEquals(2000.00, $debt->paid_amount);
        $this->assertEquals('active', $debt->status);

        $this->assertDatabaseHas('debt_payments', [
            'debt_id' => $this->debt->id,
            'amount' => 2000.00,
        ]);
    }

    public function test_multiple_payments_accumulate(): void
    {
        $this->actingAs($this->user)->post(route('debts.pay'), [
            'debt_id' => $this->debt->id,
            'amount' => 3000.00,
        ]);

        $this->actingAs($this->user)->post(route('debts.pay'), [
            'debt_id' => $this->debt->id,
            'amount' => 2000.00,
        ]);

        $debt = $this->debt->fresh();
        $this->assertEquals(5000.00, $debt->paid_amount);
        $this->assertEquals('active', $debt->status);
        $this->assertCount(2, $debt->payments);
    }

    public function test_paying_full_amount_marks_debt_as_paid(): void
    {
        $response = $this->actingAs($this->user)->post(route('debts.pay'), [
            'debt_id' => $this->debt->id,
            'amount' => 10000.00,
        ]);

        $response->assertRedirect(route('debts.index'));

        $debt = $this->debt->fresh();
        $this->assertEquals(10000.00, $debt->paid_amount);
        $this->assertEquals('paid', $debt->status);
    }

    public function test_overpaying_also_marks_debt_as_paid(): void
    {
        $response = $this->actingAs($this->user)->post(route('debts.pay'), [
            'debt_id' => $this->debt->id,
            'amount' => 12000.00,
        ]);

        $response->assertRedirect(route('debts.index'));

        $debt = $this->debt->fresh();
        $this->assertEquals(12000.00, $debt->paid_amount);
        $this->assertEquals('paid', $debt->status);
    }
}
