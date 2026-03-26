<?php

namespace App\Http\Controllers;

use App\Models\Goal;
use App\Models\GoalContribution;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GoalController extends Controller
{
    public function index()
    {
        $goals = Goal::where('user_id', Auth::id())
            ->orderByRaw("FIELD(status, 'active', 'completed', 'cancelled')")
            ->get();

        return view('goals.index', compact('goals'));
    }

    public function create()
    {
        return view('goals.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'target_amount' => 'required|numeric|min:1',
            'deadline' => 'required|date|after:today',
            'icon' => 'nullable|string|max:10',
        ]);

        Goal::create([
            'user_id' => Auth::id(),
            'name' => $request->name,
            'target_amount' => $request->target_amount,
            'deadline' => $request->deadline,
            'icon' => $request->icon ?? '🎯',
        ]);

        return redirect()->route('goals.index')->with('success', 'Meta criada!');
    }

    public function show(Request $request)
    {
        $goal = Goal::with('contributions')
            ->where('id', $request->get('id'))
            ->where('user_id', Auth::id())
            ->firstOrFail();

        return view('goals.show', compact('goal'));
    }

    public function contribute(Request $request)
    {
        $request->validate([
            'goal_id' => 'required|exists:goals,id',
            'amount' => 'required|numeric|min:0.01',
            'notes' => 'nullable|string|max:255',
        ]);

        $goal = Goal::where('id', $request->goal_id)->where('user_id', Auth::id())->firstOrFail();

        DB::transaction(function () use ($goal, $request) {
            GoalContribution::create([
                'goal_id' => $goal->id,
                'amount' => $request->amount,
                'contributed_at' => now()->toDateString(),
                'notes' => $request->notes,
            ]);

            $goal->increment('current_amount', $request->amount);

            if ($goal->fresh()->current_amount >= $goal->target_amount) {
                $goal->update(['status' => 'completed']);
            }
        });

        return redirect()->route('goals.show', ['id' => $goal->id])->with('success', 'Contribuição registrada!');
    }

    public function cancel(Request $request)
    {
        $goal = Goal::where('id', $request->id)->where('user_id', Auth::id())->firstOrFail();
        $goal->update(['status' => 'cancelled']);

        return redirect()->route('goals.index')->with('success', 'Meta cancelada.');
    }
}
