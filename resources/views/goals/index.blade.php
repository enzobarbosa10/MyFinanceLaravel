@extends('layouts.app')
@section('title', 'Metas — MyFinance')

@section('content')
<div class="card">
    <div class="filter-bar">
        <h2 style="margin-bottom: 0;">Metas Financeiras</h2>
        <a href="{{ route('goals.create') }}" class="btn btn-primary">+ Nova Meta</a>
    </div>
</div>

<div class="goals-grid">
    @forelse($goals as $goal)
        <div class="goal-card">
            <div class="goal-icon">{{ $goal->icon }}</div>
            <h3>{{ $goal->name }}</h3>
            <div class="goal-meta">
                Meta: R$ {{ number_format($goal->target_amount, 2, ',', '.') }}
                · Prazo: {{ $goal->deadline->format('d/m/Y') }}
            </div>
            <div class="progress-bar-container" style="margin-bottom:0.5rem;">
                <div class="progress-bar progress-bar-ok" style="width:{{ $goal->progressPercentage() }}%;"></div>
            </div>
            <div style="display:flex;justify-content:space-between;font-size:0.8125rem;">
                <span>R$ {{ number_format($goal->current_amount, 2, ',', '.') }}</span>
                <span style="font-weight:600;">{{ number_format($goal->progressPercentage(), 0) }}%</span>
            </div>
            @if($goal->status === 'active')
                <div style="margin-top:0.75rem;display:flex;gap:0.5rem;">
                    <a href="{{ route('goals.show', ['id' => $goal->id]) }}" class="btn btn-ghost btn-sm">Detalhes</a>
                </div>
            @elseif($goal->status === 'completed')
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
