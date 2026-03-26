<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Transaction;
use App\Models\Investment;
use App\Models\Budget;
use App\Models\Goal;
use App\Models\Debt;
use Illuminate\Support\Facades\Auth;

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

        $entradas = Transaction::sumByType($userId, 'entrada', $month);
        $saidas = Transaction::sumByType($userId, 'saida', $month);
        $saldoMes = $entradas - $saidas;

        $entradasLast = Transaction::sumByType($userId, 'entrada', $lastMonth);
        $saidasLast = Transaction::sumByType($userId, 'saida', $lastMonth);

        $investments = Investment::where('user_id', $userId)->with('asset')->get();
        $totalInvestido = $investments->sum(fn($i) => $i->totalValue());

        $recentTransactions = Transaction::byMonth($userId, $month)->take(10);

        $categoryBreakdown = Transaction::summaryByCategory($userId, $month);

        $budgets = Budget::with('category')
            ->where('user_id', $userId)
            ->where('month', $month)
            ->get();

        $goals = Goal::where('user_id', $userId)->where('status', 'active')->get();
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
        $expenseCategories = array_values(array_filter($categoryBreakdown, fn($c) => $c->type === 'saida'));

        return view('dashboard', compact(
            'saldoTotal', 'entradas', 'saidas', 'saldoMes', 'totalInvestido',
            'investments', 'recentTransactions', 'categoryBreakdown', 'budgets',
            'goals', 'debts', 'totalDividas', 'deltaEntradas', 'deltaSaidas',
            'deltaSaldo', 'saldoLast', 'mesLabel', 'greeting', 'firstName',
            'expenseCategories', 'month',
        ));
    }
}
