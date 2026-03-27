<?php

namespace App\Http\Controllers;

use App\Enums\TransactionType;
use App\Http\Requests\StoreTransactionRequest;
use App\Http\Requests\UpdateTransactionRequest;
use App\Models\Category;
use App\Models\Account;
use App\Models\Transaction;
use App\Services\TransactionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    public function __construct(
        private readonly TransactionService $transactionService,
    ) {}

    public function index(Request $request)
    {
        $userId = Auth::id();

        $filters = [
            'month'       => $request->get('month', date('Y-m')),
            'category_id' => $request->get('category_id'),
            'type'        => $request->get('type'),
        ];

        if (!preg_match('/^\d{4}-\d{2}$/', $filters['month'])) {
            $filters['month'] = date('Y-m');
        }

        $data = $this->transactionService->list($userId, $filters);

        $categories = Category::where('user_id', $userId)->orderBy('name')->get();

        return view('transactions.index', array_merge($data, [
            'categories' => $categories,
            'filters'    => $filters,
        ]));
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
        $this->transactionService->store(Auth::id(), $request->validated());

        return redirect()->route('transactions.index')->with('success', 'Transação criada com sucesso!');
    }

    public function edit(Transaction $transaction)
    {
        $this->authorize('update', $transaction);
        $userId = Auth::id();
        $categories = Category::where('user_id', $userId)->orderBy('type')->orderBy('name')->get();
        $accounts = Account::where('user_id', $userId)->get();

        return view('transactions.edit', compact('transaction', 'accounts', 'categories'));
    }

    public function update(UpdateTransactionRequest $request, Transaction $transaction)
    {
        $this->authorize('update', $transaction);

        $this->transactionService->update($transaction, $request->validated());

        return redirect()->route('transactions.index')->with('success', 'Transação atualizada!');
    }

    public function destroy(Transaction $transaction)
    {
        $this->authorize('delete', $transaction);

        $this->transactionService->destroy($transaction);

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
