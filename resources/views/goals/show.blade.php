@extends('layouts.app')
@section('title', $goal->name . ' — MyFinance')

@section('content')
<div class="card">
    <h2>{{ $goal->icon }} {{ $goal->name }}</h2>
    <div class="goal-meta">
        Meta: R$ {{ number_format($goal->target_amount, 2, ',', '.') }}
        · Prazo: {{ $goal->deadline->format('d/m/Y') }}
        · Status: <strong>{{ ucfirst($goal->status) }}</strong>
    </div>

    <div class="progress-bar-container" style="margin:1rem 0 0.5rem;">
        <div class="progress-bar progress-bar-ok" style="width:{{ $goal->progressPercentage() }}%;"></div>
    </div>
    <div style="display:flex;justify-content:space-between;font-size:0.875rem;">
        <span>R$ {{ number_format($goal->current_amount, 2, ',', '.') }} acumulado</span>
        <span style="font-weight:600;">{{ number_format($goal->progressPercentage(), 0) }}%</span>
    </div>

    @if($goal->status === 'completed')
        <div style="text-align:center;padding:1rem;margin:1rem 0;background:rgba(16,185,129,0.1);border-radius:8px;font-size:1.25rem;font-weight:700;color:var(--success);">
            🎉 Meta concluída!
        </div>
    @endif

    @if($goal->status === 'active')
        <div style="margin-top:1.5rem;">
            <h3>Adicionar Contribuição</h3>
            <form method="POST" action="{{ route('goals.contribute') }}" style="display:flex;gap:0.5rem;align-items:end;flex-wrap:wrap;margin-top:0.75rem;">
                @csrf
                <input type="hidden" name="goal_id" value="{{ $goal->id }}">
                <div>
                    <label for="amount">Valor (R$)</label>
                    <input type="number" id="amount" name="amount" step="0.01" min="0.01" required style="width:150px;">
                </div>
                <div>
                    <label for="notes">Nota (opcional)</label>
                    <input type="text" id="notes" name="notes" maxlength="255" placeholder="Observação" style="width:200px;">
                </div>
                <button type="submit" class="btn btn-primary btn-sm">Contribuir</button>
            </form>
        </div>

        <div style="margin-top:1rem;">
            <form method="POST" action="{{ route('goals.cancel') }}" onsubmit="return confirm('Cancelar esta meta?')">
                @csrf
                <input type="hidden" name="id" value="{{ $goal->id }}">
                <button type="submit" class="btn btn-danger btn-sm">Cancelar Meta</button>
            </form>
        </div>
    @endif
</div>

@if($goal->contributions->count() > 0)
<div class="card">
    <h3>Histórico de Contribuições</h3>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Data</th>
                    <th>Valor</th>
                    <th>Nota</th>
                </tr>
            </thead>
            <tbody>
                @foreach($goal->contributions->sortByDesc('contributed_at') as $c)
                    <tr>
                        <td>{{ $c->contributed_at->format('d/m/Y') }}</td>
                        <td class="value-positive">R$ {{ number_format($c->amount, 2, ',', '.') }}</td>
                        <td>{{ $c->notes ?: '—' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

<a href="{{ route('goals.index') }}" class="btn btn-ghost">← Voltar para Metas</a>
@endsection
