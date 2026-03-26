<?php

namespace App\Http\Controllers;

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
            ->get()
            ->map(function ($budget) use ($userId, $month) {
                $spent = Transaction::where('user_id', $userId)
                    ->where('category_id', $budget->category_id)
                    ->where('type', 'saida')
                    ->whereRaw("DATE_FORMAT(transaction_at, '%Y-%m') = ?", [$month])
                    ->sum('amount');

                $budget->spent = $spent;
                $budget->percentage = $budget->amount > 0
                    ? round(($spent / $budget->amount) * 100, 1)
                    : 0;

                return $budget;
            });

        return view('budgets.index', compact('budgets', 'month'));
    }

    public function create()
    {
        $categories = Category::where('user_id', Auth::id())
            ->where('type', 'saida')
            ->orderBy('name')
            ->get();

        return view('budgets.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'amount' => 'required|numeric|min:0.01',
            'month' => 'required|regex:/^\d{4}-\d{2}$/',
        ]);

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

    public function destroy(Request $request)
    {
        Budget::where('id', $request->id)->where('user_id', Auth::id())->delete();
        return redirect()->route('budgets.index')->with('success', 'Orçamento removido.');
    }
}
