<?php

namespace App\Http\Controllers;

use App\Enums\TransactionType;
use App\Http\Requests\StoreTransactionRequest;
use App\Models\Transaction;
use App\Models\Category;
use App\Models\Account;
use App\Models\Budget;
use App\Models\BudgetAlert;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $userId = Auth::id();
        $month = $request->get('month', date('Y-m'));

        if (!preg_match('/^\d{4}-\d{2}$/', $month)) {
            $month = date('Y-m');
        }

        $entradas = Transaction::sumByType($userId, TransactionType::Entrada, $month);
        $saidas = Transaction::sumByType($userId, TransactionType::Saida, $month);
        $saldo = $entradas - $saidas;
        $transactions = Transaction::byMonth($userId, $month);

        return view('transactions.index', compact('transactions', 'entradas', 'saidas', 'saldo', 'month'));
    }

    public function create()
    {
        $userId = Auth::id();
        $categories = Category::where('user_id', $userId)->orderBy('type')->orderBy('name')->get();

        if ($categories->isEmpty()) {
            Category::seedDefaults($userId);
            $categories = Category::where('user_id', $userId)->orderBy('type')->orderBy('name')->get();
        }

        $accounts = Account::where('user_id', $userId)->get();

        return view('transactions.create', compact('accounts', 'categories'));
    }

    public function store(StoreTransactionRequest $request)
    {
        $userId = Auth::id();

        // Verify ownership
        $account = Account::where('id', $request->account_id)->where('user_id', $userId)->firstOrFail();
        $category = Category::where('id', $request->category_id)->where('user_id', $userId)->firstOrFail();

        $transaction = Transaction::createWithBalance([
            'user_id' => $userId,
            'account_id' => $request->account_id,
            'category_id' => $request->category_id,
            'type' => $request->type,
            'amount' => $request->amount,
            'description' => $request->description,
            'transaction_at' => $request->transaction_at,
        ]);

        // Check budget alerts for "saida" transactions
        if ($transaction->type === TransactionType::Saida) {
            $this->checkBudgetAlerts($userId, $transaction->category_id, $transaction->transaction_at->format('Y-m'));
        }

        return redirect()->route('transactions.index')->with('success', 'Transação criada com sucesso!');
    }

    protected function checkBudgetAlerts(int $userId, int $categoryId, string $month): void
    {
        $budget = Budget::where('user_id', $userId)
            ->where('category_id', $categoryId)
            ->where('month', $month)
            ->first();

        if (! $budget || $budget->amount <= 0) {
            return;
        }

        $spent = Transaction::forUser($userId)
            ->ofType(TransactionType::Saida)
            ->where('category_id', $categoryId)
            ->forMonth($month)
            ->sum('amount');

        $percentage = round(($spent / $budget->amount) * 100, 1);

        if ($percentage >= 100) {
            BudgetAlert::updateOrCreate(
                ['budget_id' => $budget->id, 'alert_type' => 'exceeded', 'month' => $month],
                ['user_id' => $userId, 'percentage' => $percentage, 'seen' => false]
            );
        }

        if ($percentage >= 80) {
            BudgetAlert::updateOrCreate(
                ['budget_id' => $budget->id, 'alert_type' => 'warning', 'month' => $month],
                ['user_id' => $userId, 'percentage' => $percentage, 'seen' => false]
            );
        }
    }

    public function edit(Transaction $transaction)
    {
        $this->authorize('update', $transaction);
        $userId = Auth::id();
        $categories = Category::where('user_id', $userId)->orderBy('type')->orderBy('name')->get();
        $accounts = Account::where('user_id', $userId)->get();
        return view('transactions.edit', compact('transaction', 'accounts', 'categories'));
    }

    public function update(StoreTransactionRequest $request, Transaction $transaction)
    {
        $this->authorize('update', $transaction);
        $userId = Auth::id();

        Account::where('id', $request->account_id)->where('user_id', $userId)->firstOrFail();
        Category::where('id', $request->category_id)->where('user_id', $userId)->firstOrFail();

        Transaction::updateWithBalance($transaction, [
            'account_id' => $request->account_id,
            'category_id' => $request->category_id,
            'type' => $request->type,
            'amount' => $request->amount,
            'description' => $request->description,
            'transaction_at' => $request->transaction_at,
        ]);

        // Re-check budget alerts if it's a "saida"
        if ($transaction->fresh()->type === TransactionType::Saida) {
            $this->checkBudgetAlerts($userId, $transaction->category_id, $transaction->transaction_at->format('Y-m'));
        }

        return redirect()->route('transactions.index')->with('success', 'Transação atualizada!');
    }

    public function destroy(Transaction $transaction)
    {
        $this->authorize('delete', $transaction);

        Transaction::deleteWithBalance($transaction);

        return redirect()->route('transactions.index')->with('success', 'Transação excluída.');
    }

    public function categoriesByType(Request $request)
    {
        $type = TransactionType::tryFrom($request->get('type'));
        if (!$type) {
            return response()->json([]);
        }

        $categories = Category::where('user_id', Auth::id())
            ->where('type', $type)
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json($categories);
    }
}
