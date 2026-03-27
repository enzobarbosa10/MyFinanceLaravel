<?php

namespace App\Http\Controllers;

use App\Enums\GoalStatus;
use App\Http\Requests\StoreGoalRequest;
use App\Http\Requests\UpdateGoalRequest;
use App\Models\Goal;
use App\Services\GoalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GoalController extends Controller
{
    public function __construct(
        protected GoalService $goalService
    ) {}

    public function index()
    {
        $goals = Goal::where('user_id', Auth::id())
            ->orderByRaw("FIELD(status, ?, ?, ?)", [
                GoalStatus::Active->value,
                GoalStatus::Completed->value,
                GoalStatus::Cancelled->value,
            ])
            ->get();

        $analyses = $goals->mapWithKeys(fn (Goal $goal) => [
            $goal->id => $this->goalService->getAnalysis($goal),
        ]);

        return view('goals.index', compact('goals', 'analyses'));
    }

    public function create()
    {
        return view('goals.create');
    }

    public function store(StoreGoalRequest $request)
    {

        Goal::create([
            'user_id' => Auth::id(),
            'name' => $request->name,
            'target_amount' => $request->target_amount,
            'deadline' => $request->deadline,
            'icon' => $request->icon ?? '🎯',
        ]);

        return redirect()->route('goals.index')->with('success', 'Meta criada!');
    }

    public function show(Goal $goal)
    {
        $goal->load('contributions');
        $this->authorize('update', $goal);

        $analysis = $this->goalService->getAnalysis($goal);
        $estimatedDate = $this->goalService->estimatedCompletionDate($goal);

        return view('goals.show', compact('goal', 'analysis', 'estimatedDate'));
    }

    public function contribute(Goal $goal, Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'notes' => 'nullable|string|max:255',
        ]);

        $this->authorize('update', $goal);

        $this->goalService->contribute($goal, (float) $request->amount, $request->notes);

        return redirect()->route('goals.show', $goal)->with('success', 'Contribuição registrada!');
    }

    public function cancel(Goal $goal)
    {
        $this->authorize('delete', $goal);
        $goal->update(['status' => GoalStatus::Cancelled]);

        return redirect()->route('goals.index')->with('success', 'Meta cancelada.');
    }

    public function edit(Goal $goal)
    {
        $this->authorize('update', $goal);
        return view('goals.edit', compact('goal'));
    }

    public function update(UpdateGoalRequest $request, Goal $goal)
    {
        $this->authorize('update', $goal);

        $goal->update([
            'name' => $request->name,
            'target_amount' => $request->target_amount,
            'deadline' => $request->deadline,
            'icon' => $request->icon ?? $goal->icon,
        ]);

        return redirect()->route('goals.index')->with('success', 'Meta atualizada!');
    }
}
