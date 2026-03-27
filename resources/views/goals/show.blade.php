@extends('layouts.app')
@section('title', $goal->name . ' — MyFinance')
@use(App\Enums\GoalStatus)

@section('content')
<div class="card">
    <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:0.5rem;">
        <h2 style="margin:0;">{{ $goal->icon }} {{ $goal->name }}</h2>
        @if($goal->status === GoalStatus::Active)
            <a href="{{ route('goals.edit', $goal) }}" class="btn btn-ghost btn-sm">Editar</a>
        @endif
    </div>
    <div class="goal-meta">
        Meta: R$ {{ number_format($goal->target_amount, 2, ',', '.') }}
        · Prazo: {{ $goal->deadline->format('d/m/Y') }}
        · Status: <strong>{{ ucfirst($goal->status->value) }}</strong>
    </div>

    <div class="progress-bar-container" style="margin:1rem 0 0.5rem;">
        @php
            $barClass = match($analysis['progress_status']) {
                'ahead', 'completed' => 'progress-bar-ok',
                'behind' => 'progress-bar-warning',
                'overdue' => 'progress-bar-danger',
                default => 'progress-bar-ok',
            };
        @endphp
        <div class="progress-bar {{ $barClass }}" style="width:{{ $goal->progressPercentage() }}%;"></div>
    </div>
    <div style="display:flex;justify-content:space-between;font-size:0.875rem;">
        <span>R$ {{ number_format($goal->current_amount, 2, ',', '.') }} acumulado</span>
        <span style="font-weight:600;">{{ number_format($goal->progressPercentage(), 0) }}%</span>
    </div>

    {{-- Alertas --}}
    @foreach($analysis['alerts'] as $alert)
        <div style="margin-top:0.75rem;padding:0.625rem 0.75rem;border-radius:8px;font-size:0.875rem;
            @if($alert['type'] === 'danger') background:rgba(239,68,68,0.08);color:var(--danger);border-left:3px solid var(--danger);
            @elseif($alert['type'] === 'warning') background:rgba(245,158,11,0.08);color:#f59e0b;border-left:3px solid #f59e0b;
            @elseif($alert['type'] === 'success') background:rgba(16,185,129,0.08);color:var(--success);border-left:3px solid var(--success);
            @else background:rgba(59,130,246,0.08);color:#3b82f6;border-left:3px solid #3b82f6; @endif">
            {{ $alert['message'] }}
        </div>
    @endforeach

    @if($goal->status === GoalStatus::Completed)
        <div style="text-align:center;padding:1rem;margin:1rem 0;background:rgba(16,185,129,0.1);border-radius:8px;font-size:1.25rem;font-weight:700;color:var(--success);">
            🎉 Meta concluída!
        </div>
    @endif
</div>

{{-- Painel de Análise --}}
@if($goal->status === GoalStatus::Active)
<div class="card">
    <h3>📊 Análise da Meta</h3>
    <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(180px, 1fr));gap:1rem;margin-top:0.75rem;">
        <div style="padding:0.75rem;background:var(--bg-secondary, #f8f9fa);border-radius:8px;text-align:center;">
            <div style="font-size:0.75rem;color:var(--text-secondary);margin-bottom:0.25rem;">Valor Mensal Necessário</div>
            <div style="font-size:1.25rem;font-weight:700;color:var(--primary);">
                R$ {{ number_format($analysis['monthly_required'], 2, ',', '.') }}
            </div>
        </div>
        <div style="padding:0.75rem;background:var(--bg-secondary, #f8f9fa);border-radius:8px;text-align:center;">
            <div style="font-size:0.75rem;color:var(--text-secondary);margin-bottom:0.25rem;">Média Mensal Atual</div>
            <div style="font-size:1.25rem;font-weight:700;">
                R$ {{ number_format($analysis['monthly_average'], 2, ',', '.') }}
            </div>
        </div>
        <div style="padding:0.75rem;background:var(--bg-secondary, #f8f9fa);border-radius:8px;text-align:center;">
            <div style="font-size:0.75rem;color:var(--text-secondary);margin-bottom:0.25rem;">Meses Restantes</div>
            <div style="font-size:1.25rem;font-weight:700;">
                {{ $analysis['remaining_months'] }}
            </div>
        </div>
        <div style="padding:0.75rem;background:var(--bg-secondary, #f8f9fa);border-radius:8px;text-align:center;">
            <div style="font-size:0.75rem;color:var(--text-secondary);margin-bottom:0.25rem;">Falta</div>
            <div style="font-size:1.25rem;font-weight:700;color:var(--danger);">
                R$ {{ number_format($goal->remaining_amount, 2, ',', '.') }}
            </div>
        </div>
    </div>

    @if($estimatedDate)
        <div style="margin-top:1rem;font-size:0.875rem;color:var(--text-secondary);">
            📅 Estimativa de conclusão (com base na média): <strong>{{ $estimatedDate->format('d/m/Y') }}</strong>
            @if($estimatedDate->gt($goal->deadline))
                <span style="color:var(--danger);font-weight:600;"> — após o prazo!</span>
            @else
                <span style="color:var(--success);font-weight:600;"> — dentro do prazo</span>
            @endif
        </div>
    @elseif($goal->current_amount <= 0)
        <div style="margin-top:1rem;font-size:0.875rem;color:var(--text-secondary);">
            📅 Faça sua primeira contribuição para ver a estimativa de conclusão.
        </div>
    @endif
</div>
@endif

{{-- Contribuição --}}
@if($goal->status === GoalStatus::Active)
<div class="card">
    <h3>Adicionar Contribuição</h3>
    <form method="POST" action="{{ route('goals.contribute', $goal) }}" style="display:flex;gap:0.5rem;align-items:end;flex-wrap:wrap;margin-top:0.75rem;">
        @csrf
        <div>
            <label for="amount">Valor (R$)</label>
            <input type="number" id="amount" name="amount" step="0.01" min="0.01" required style="width:150px;"
                   placeholder="{{ number_format($analysis['monthly_required'], 2, '.', '') }}">
        </div>
        <div>
            <label for="notes">Nota (opcional)</label>
            <input type="text" id="notes" name="notes" maxlength="255" placeholder="Observação" style="width:200px;">
        </div>
        <button type="submit" class="btn btn-primary btn-sm">Contribuir</button>
    </form>

    <div style="margin-top:1rem;">
        <form method="POST" action="{{ route('goals.cancel', $goal) }}" onsubmit="return confirm('Cancelar esta meta?')">
            @csrf
            <button type="submit" class="btn btn-danger btn-sm">Cancelar Meta</button>
        </form>
    </div>
</div>
@endif

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
