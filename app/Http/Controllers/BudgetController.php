<?php

namespace App\Http\Controllers;

use App\Enums\TransactionType;
use App\Http\Requests\StoreBudgetRequest;
use App\Models\Budget;
use App\Models\Category;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BudgetController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        $month = date('Y-m');

        $budgets = Budget::with('category')
            ->where('user_id', $userId)
            ->where('month', $month)
            ->get();

        // Single query to fetch all spent amounts grouped by category
        $spentByCategory = Transaction::forUser($userId)
            ->ofType(TransactionType::Saida)
            ->forMonth($month)
            ->whereIn('category_id', $budgets->pluck('category_id'))
            ->groupBy('category_id')
            ->selectRaw('category_id, SUM(amount) as total')
            ->pluck('total', 'category_id');

        $budgets->each(function ($budget) use ($spentByCategory) {
            $budget->spent = (float) ($spentByCategory[$budget->category_id] ?? 0);
            $budget->percentage = $budget->amount > 0
                ? round(($budget->spent / $budget->amount) * 100, 1)
                : 0;
        });

        return view('budgets.index', compact('budgets', 'month'));
    }

    public function create()
    {
        $categories = Category::where('user_id', Auth::id())
            ->where('type', TransactionType::Saida)
            ->orderBy('name')
            ->get();

        return view('budgets.create', compact('categories'));
    }

    public function store(StoreBudgetRequest $request)
    {
        $userId = Auth::id();
        Category::where('id', $request->category_id)->where('user_id', $userId)->firstOrFail();

        Budget::updateOrCreate(
            [
                'user_id' => $userId,
                'category_id' => $request->category_id,
                'month' => $request->month,
            ],
            ['amount' => $request->amount]
        );

        return redirect()->route('budgets.index')->with('success', 'Orçamento salvo!');
    }

    public function destroy(Budget $budget)
    {
        if ($budget->user_id !== Auth::id()) {
            abort(403);
        }

        $budget->delete();
        return redirect()->route('budgets.index')->with('success', 'Orçamento removido.');
    }
}
