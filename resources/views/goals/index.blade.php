@extends('layouts.app')
@section('title', 'Metas — MyFinance')
@use(App\Enums\GoalStatus)

@section('content')
<div class="card">
    <div class="filter-bar">
        <h2 style="margin-bottom: 0;">Metas Financeiras</h2>
        <a href="{{ route('goals.create') }}" class="btn btn-primary">+ Nova Meta</a>
    </div>
</div>

<div class="goals-grid">
    @forelse($goals as $goal)
        @php $a = $analyses[$goal->id]; @endphp
        <div class="goal-card">
            <div class="goal-icon">{{ $goal->icon }}</div>
            <h3>{{ $goal->name }}</h3>
            <div class="goal-meta">
                Meta: R$ {{ number_format($goal->target_amount, 2, ',', '.') }}
                · Prazo: {{ $goal->deadline->format('d/m/Y') }}
            </div>

            @if($goal->status === GoalStatus::Active && $a['monthly_required'] > 0)
                <div style="font-size:0.8125rem;color:var(--text-secondary);margin-bottom:0.5rem;">
                    💰 R$ {{ number_format($a['monthly_required'], 2, ',', '.') }}/mês necessário
                    @if($a['remaining_months'] > 0)
                        · {{ $a['remaining_months'] }} {{ $a['remaining_months'] === 1 ? 'mês restante' : 'meses restantes' }}
                    @endif
                </div>
            @endif

            <div class="progress-bar-container" style="margin-bottom:0.5rem;">
                @php
                    $barClass = match($a['progress_status']) {
                        'ahead', 'completed' => 'progress-bar-ok',
                        'behind' => 'progress-bar-warning',
                        'overdue' => 'progress-bar-danger',
                        default => 'progress-bar-ok',
                    };
                @endphp
                <div class="progress-bar {{ $barClass }}" style="width:{{ $goal->progressPercentage() }}%;"></div>
            </div>
            <div style="display:flex;justify-content:space-between;font-size:0.8125rem;">
                <span>R$ {{ number_format($goal->current_amount, 2, ',', '.') }}</span>
                <span style="font-weight:600;">{{ number_format($goal->progressPercentage(), 0) }}%</span>
            </div>

            {{-- Alertas resumidos --}}
            @if(!empty($a['alerts']))
                @php $alert = $a['alerts'][0]; @endphp
                <div style="margin-top:0.5rem;font-size:0.75rem;padding:0.375rem 0.5rem;border-radius:6px;
                    @if($alert['type'] === 'danger') background:rgba(239,68,68,0.08);color:var(--danger);
                    @elseif($alert['type'] === 'warning') background:rgba(245,158,11,0.08);color:#f59e0b;
                    @elseif($alert['type'] === 'success') background:rgba(16,185,129,0.08);color:var(--success);
                    @else background:rgba(59,130,246,0.08);color:#3b82f6; @endif">
                    {{ $alert['message'] }}
                </div>
            @endif

            @if($goal->status === GoalStatus::Active)
                <div style="margin-top:0.75rem;display:flex;gap:0.5rem;">
                    <a href="{{ route('goals.show', $goal) }}" class="btn btn-ghost btn-sm">Detalhes</a>
                </div>
            @elseif($goal->status === GoalStatus::Completed)
                <div style="text-align:center;padding:0.5rem;background:rgba(16,185,129,0.08);border-radius:8px;margin-top:0.5rem;font-weight:600;color:var(--success);">
                    🎉 Concluída!
                </div>
            @else
                <div style="text-align:center;padding:0.5rem;color:var(--text-secondary);margin-top:0.5rem;font-size:0.8125rem;">
                    Cancelada
                </div>
            @endif
        </div>
    @empty
        <div class="card"><p class="text-muted">Nenhuma meta cadastrada.</p></div>
    @endforelse
</div>
@endsection
