<?php

namespace Tests\Feature;

use App\Enums\TransactionType;
use App\Models\Account;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransactionTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Account $account;
    private Category $entradaCategory;
    private Category $saidaCategory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();

        $this->account = Account::create([
            'user_id' => $this->user->id,
            'name' => 'Conta Corrente',
            'balance' => 1000.00,
            'type' => 'corrente',
        ]);

        $this->entradaCategory = Category::create([
            'user_id' => $this->user->id,
            'name' => 'Salário',
            'type' => TransactionType::Entrada->value,
        ]);

        $this->saidaCategory = Category::create([
            'user_id' => $this->user->id,
            'name' => 'Alimentação',
            'type' => TransactionType::Saida->value,
        ]);
    }

    public function test_creating_entrada_transaction_increments_account_balance(): void
    {
        $response = $this->actingAs($this->user)->post(route('transactions.store'), [
            'account_id' => $this->account->id,
            'category_id' => $this->entradaCategory->id,
            'type' => TransactionType::Entrada->value,
            'amount' => 500.00,
            'description' => 'Salário mensal',
            'transaction_at' => now()->toDateString(),
        ]);

        $response->assertRedirect(route('transactions.index'));

        $this->assertDatabaseHas('transactions', [
            'user_id' => $this->user->id,
            'type' => TransactionType::Entrada->value,
            'amount' => 500.00,
        ]);

        $this->assertEquals(1500.00, $this->account->fresh()->balance);
    }

    public function test_creating_saida_transaction_decrements_account_balance(): void
    {
        $response = $this->actingAs($this->user)->post(route('transactions.store'), [
            'account_id' => $this->account->id,
            'category_id' => $this->saidaCategory->id,
            'type' => TransactionType::Saida->value,
            'amount' => 200.00,
            'description' => 'Supermercado',
            'transaction_at' => now()->toDateString(),
        ]);

        $response->assertRedirect(route('transactions.index'));

        $this->assertDatabaseHas('transactions', [
            'user_id' => $this->user->id,
            'type' => TransactionType::Saida->value,
            'amount' => 200.00,
        ]);

        $this->assertEquals(800.00, $this->account->fresh()->balance);
    }

    public function test_deleting_entrada_transaction_reverts_balance(): void
    {
        // Create a transaction first
        $transaction = Transaction::createWithBalance([
            'user_id' => $this->user->id,
            'account_id' => $this->account->id,
            'category_id' => $this->entradaCategory->id,
            'type' => TransactionType::Entrada,
            'amount' => 300.00,
            'description' => 'Freelance',
            'transaction_at' => now(),
        ]);

        $this->assertEquals(1300.00, $this->account->fresh()->balance);

        // Delete via controller
        $response = $this->actingAs($this->user)->delete(route('transactions.destroy', $transaction));

        $response->assertRedirect(route('transactions.index'));
        $this->assertDatabaseMissing('transactions', ['id' => $transaction->id]);
        $this->assertEquals(1000.00, $this->account->fresh()->balance);
    }

    public function test_deleting_saida_transaction_reverts_balance(): void
    {
        $transaction = Transaction::createWithBalance([
            'user_id' => $this->user->id,
            'account_id' => $this->account->id,
            'category_id' => $this->saidaCategory->id,
            'type' => TransactionType::Saida,
            'amount' => 150.00,
            'description' => 'Farmácia',
            'transaction_at' => now(),
        ]);

        $this->assertEquals(850.00, $this->account->fresh()->balance);

        $response = $this->actingAs($this->user)->delete(route('transactions.destroy', $transaction));

        $response->assertRedirect(route('transactions.index'));
        $this->assertDatabaseMissing('transactions', ['id' => $transaction->id]);
        $this->assertEquals(1000.00, $this->account->fresh()->balance);
    }
}
