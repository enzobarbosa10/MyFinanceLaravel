<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Category;
use App\Models\Account;
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

        $entradas = Transaction::sumByType($userId, 'entrada', $month);
        $saidas = Transaction::sumByType($userId, 'saida', $month);
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

    public function store(Request $request)
    {
        $request->validate([
            'account_id' => 'required|exists:accounts,id',
            'category_id' => 'required|exists:categories,id',
            'type' => 'required|in:entrada,saida',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string|max:255',
            'transaction_at' => 'required|date',
        ]);

        $userId = Auth::id();

        // Verify ownership
        $account = Account::where('id', $request->account_id)->where('user_id', $userId)->firstOrFail();
        $category = Category::where('id', $request->category_id)->where('user_id', $userId)->firstOrFail();

        Transaction::createWithBalance([
            'user_id' => $userId,
            'account_id' => $request->account_id,
            'category_id' => $request->category_id,
            'type' => $request->type,
            'amount' => $request->amount,
            'description' => $request->description,
            'transaction_at' => $request->transaction_at,
        ]);

        return redirect()->route('transactions.index')->with('success', 'Transação criada com sucesso!');
    }

    public function destroy(Request $request)
    {
        $userId = Auth::id();
        $transaction = Transaction::where('id', $request->id)->where('user_id', $userId)->firstOrFail();

        Transaction::deleteWithBalance($transaction);

        return redirect()->route('transactions.index')->with('success', 'Transação excluída.');
    }

    public function categoriesByType(Request $request)
    {
        $type = $request->get('type');
        if (!in_array($type, ['entrada', 'saida'])) {
            return response()->json([]);
        }

        $categories = Category::where('user_id', Auth::id())
            ->where('type', $type)
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json($categories);
    }
}
