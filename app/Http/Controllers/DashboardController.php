<?php

namespace App\Http\Controllers;

use App\Enums\GoalStatus;
use App\Enums\TransactionType;
use App\Models\Account;
use App\Models\Transaction;
use App\Models\Investment;
use App\Models\Budget;
use App\Models\BudgetAlert;
use App\Models\Goal;
use App\Models\Debt;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $userId = $user->id;
        $month = date('Y-m');
        $lastMonth = date('Y-m', strtotime('-1 month'));

        $accounts = Account::where('user_id', $userId)->get();
        $saldoTotal = $accounts->sum('balance');

        $entradas = Transaction::sumByType($userId, TransactionType::Entrada, $month);
        $saidas = Transaction::sumByType($userId, TransactionType::Saida, $month);
        $saldoMes = $entradas - $saidas;

        $entradasLast = Transaction::sumByType($userId, TransactionType::Entrada, $lastMonth);
        $saidasLast = Transaction::sumByType($userId, TransactionType::Saida, $lastMonth);

        $investments = Investment::where('user_id', $userId)->with('asset')->get();
        $totalInvestido = Cache::remember("dashboard:{$userId}:totalInvestido", 60, function () use ($investments) {
            return $investments->sum(fn($i) => $i->totalValue());
        });

        $recentTransactions = Transaction::with(['account', 'category'])
            ->forUser($userId)
            ->forMonth($month)
            ->orderByDesc('transaction_at')
            ->orderByDesc('id')
            ->take(10)
            ->get();

        $categoryBreakdown = Cache::remember("dashboard:{$userId}:categoryBreakdown", 60, function () use ($userId, $month) {
            return Transaction::summaryByCategory($userId, $month);
        });

        $budgets = Cache::remember("dashboard:{$userId}:budgets", 60, function () use ($userId, $month) {
            return Budget::with('category')
                ->where('user_id', $userId)
                ->where('month', $month)
                ->get();
        });

        $goals = Goal::where('user_id', $userId)->where('status', GoalStatus::Active)->get();
        $debts = Debt::where('user_id', $userId)->where('status', 'active')->get();
        $totalDividas = $debts->sum(fn($d) => $d->remainingBalance());

        // Deltas
        $deltaEntradas = $entradasLast > 0 ? round((($entradas - $entradasLast) / $entradasLast) * 100, 0) : 0;
        $deltaSaidas = $saidasLast > 0 ? round((($saidas - $saidasLast) / $saidasLast) * 100, 0) : 0;

        $saldoLast = $entradasLast - $saidasLast;
        $deltaSaldo = $saldoLast != 0 ? round(abs(($saldoMes - $saldoLast) / $saldoLast) * 100, 0) : 0;

        // Mês label
        $mesesPt = ['Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'];
        $mesLabel = $mesesPt[(int)date('n') - 1] . ' ' . date('Y');

        // Greeting
        $hour = (int) date('H');
        $greeting = $hour < 12 ? 'Bom dia' : ($hour < 18 ? 'Boa tarde' : 'Boa noite');
        $firstName = explode(' ', trim($user->name))[0];

        // Expense categories for chart
        $expenseCategories = array_values(array_filter($categoryBreakdown, fn($c) => $c['type'] === TransactionType::Saida->value));

        $budgetAlerts = BudgetAlert::with('budget.category')
            ->where('user_id', $userId)
            ->where('month', $month)
            ->where('seen', false)
            ->orderByDesc('percentage')
            ->get();

        return view('dashboard', compact(
            'saldoTotal', 'entradas', 'saidas', 'saldoMes', 'totalInvestido',
            'investments', 'recentTransactions', 'categoryBreakdown', 'budgets',
            'goals', 'debts', 'totalDividas', 'deltaEntradas', 'deltaSaidas',
            'deltaSaldo', 'saldoLast', 'mesLabel', 'greeting', 'firstName',
            'expenseCategories', 'month', 'budgetAlerts',
        ));
    }
}
