<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDebtRequest;
use App\Http\Requests\UpdateDebtRequest;
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

    public function store(StoreDebtRequest $request)
    {

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

        $debt = Debt::findOrFail($request->debt_id);
        $this->authorize('update', $debt);

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

    public function edit(Debt $debt)
    {
        $this->authorize('update', $debt);
        return view('debts.edit', compact('debt'));
    }

    public function update(UpdateDebtRequest $request, Debt $debt)
    {
        $this->authorize('update', $debt);

        $debt->update([
            'name' => $request->name,
            'total_amount' => $request->total_amount,
            'monthly_interest_rate' => $request->monthly_interest_rate ?? 0,
            'min_payment' => $request->min_payment ?? 0,
            'due_day' => $request->due_day,
            'creditor' => $request->creditor,
        ]);

        return redirect()->route('debts.index')->with('success', 'Dívida atualizada!');
    }
}
