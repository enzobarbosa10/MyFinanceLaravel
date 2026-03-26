<?php

namespace App\Http\Controllers;

use App\Models\Debt;
use App\Models\DebtPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DebtController extends Controller
{
    public function index()
    {
        $debts = Debt::where('user_id', Auth::id())
            ->orderByRaw("FIELD(status, 'active', 'paid')")
            ->get()
            ->map(function ($debt) {
                $debt->remaining = $debt->remainingBalance();
                $debt->percentage = $debt->total_amount > 0
                    ? round(($debt->paid_amount / $debt->total_amount) * 100, 1)
                    : 0;
                return $debt;
            });

        return view('debts.index', compact('debts'));
    }

    public function create()
    {
        return view('debts.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'total_amount' => 'required|numeric|min:0.01',
            'monthly_interest_rate' => 'nullable|numeric|min:0',
            'min_payment' => 'nullable|numeric|min:0',
            'due_day' => 'required|integer|min:1|max:31',
            'creditor' => 'nullable|string|max:100',
        ]);

        Debt::create([
            'user_id' => Auth::id(),
            'name' => $request->name,
            'total_amount' => $request->total_amount,
            'monthly_interest_rate' => $request->monthly_interest_rate ?? 0,
            'min_payment' => $request->min_payment ?? 0,
            'due_day' => $request->due_day,
            'creditor' => $request->creditor,
        ]);

        return redirect()->route('debts.index')->with('success', 'Dívida registrada!');
    }

    public function pay(Request $request)
    {
        $request->validate([
            'debt_id' => 'required|exists:debts,id',
            'amount' => 'required|numeric|min:0.01',
            'notes' => 'nullable|string|max:255',
        ]);

        $debt = Debt::where('id', $request->debt_id)->where('user_id', Auth::id())->firstOrFail();

        DB::transaction(function () use ($debt, $request) {
            DebtPayment::create([
                'debt_id' => $debt->id,
                'amount' => $request->amount,
                'paid_at' => now()->toDateString(),
                'notes' => $request->notes,
            ]);

            $debt->increment('paid_amount', $request->amount);

            if ($debt->fresh()->paid_amount >= $debt->total_amount) {
                $debt->update(['status' => 'paid']);
            }
        });

        return redirect()->route('debts.index')->with('success', 'Pagamento registrado!');
    }
}
