@extends('layouts.app')
@section('title', 'Dashboard — MyFinance')

@section('content')
<!-- TOPBAR -->
<div class="dash-topbar">
    <div class="topbar-left">
        <h1 class="dash-title">{{ $greeting }}, {{ $firstName }} 👋</h1>
        <p class="dash-subtitle">Aqui está o que aconteceu com seu dinheiro esse mês</p>
    </div>
    <div class="topbar-right">
        <span class="month-badge">📅 {{ $mesLabel }}</span>
        <a href="{{ route('transactions.create') }}" class="btn-action">+ Nova Transação</a>
    </div>
</div>

<!-- KPIs -->
<div class="kpi-grid">
    <div class="kpi-card">
        <div class="kpi-top">
            <span class="kpi-label">Saldo Total</span>
            <div class="kpi-icon" style="background:rgba(74,222,128,0.1)">💰</div>
        </div>
        <div class="kpi-value" style="color:#4ade80">R$ {{ number_format($saldoTotal, 2, ',', '.') }}</div>
        <div class="kpi-delta {{ $saldoMes >= $saldoLast ? 'delta-up' : 'delta-down' }}">
            {{ $saldoMes >= $saldoLast ? '↑' : '↓' }} {{ $deltaSaldo }}% vs mês anterior
        </div>
    </div>
    <div class="kpi-card">
        <div class="kpi-top">
            <span class="kpi-label">Entradas</span>
            <div class="kpi-icon" style="background:rgba(184,255,87,0.12)">↑</div>
        </div>
        <div class="kpi-value" style="color:var(--accent)">R$ {{ number_format($entradas, 2, ',', '.') }}</div>
        <div class="kpi-delta {{ $deltaEntradas >= 0 ? 'delta-up' : 'delta-down' }}">
            @if($deltaEntradas == 0)
                = igual ao mês passado
            @else
                {{ $deltaEntradas > 0 ? '↑' : '↓' }} {{ abs($deltaEntradas) }}% vs mês anterior
            @endif
        </div>
    </div>
    <div class="kpi-card">
        <div class="kpi-top">
            <span class="kpi-label">Saídas</span>
            <div class="kpi-icon" style="background:rgba(248,113,113,0.12)">↓</div>
        </div>
        <div class="kpi-value" style="color:var(--danger)">R$ {{ number_format($saidas, 2, ',', '.') }}</div>
        <div class="kpi-delta {{ $deltaSaidas > 0 ? 'delta-down' : 'delta-up' }}">
            @if($deltaSaidas == 0)
                = igual ao mês passado
            @else
                {{ $deltaSaidas > 0 ? '↑' : '↓' }} {{ abs($deltaSaidas) }}% vs mês anterior
            @endif
        </div>
    </div>
    <div class="kpi-card">
        <div class="kpi-top">
            <span class="kpi-label">Investido</span>
            <div class="kpi-icon" style="background:rgba(96,165,250,0.12)">📈</div>
        </div>
        <div class="kpi-value" style="color:var(--blue)">R$ {{ number_format($totalInvestido, 2, ',', '.') }}</div>
        <div class="kpi-delta delta-neutral">
            {{ $investments->count() }} ativo(s) em carteira
        </div>
    </div>
</div>

<!-- CHARTS ROW -->
@if($budgetAlerts->count() > 0)
<div class="card" style="border-left:4px solid var(--danger);margin-bottom:1.5rem;">
    <div class="card-header">
        <span class="card-title">⚠️ Alertas de Orçamento</span>
    </div>
    @foreach($budgetAlerts as $alert)
        <div style="display:flex;align-items:center;justify-content:space-between;padding:0.5rem 0;border-bottom:1px solid var(--border);">
            <div>
                <span style="font-weight:600;">{{ $alert->budget->category->name ?? 'Categoria' }}</span>
                @if($alert->alert_type === 'exceeded')
                    <span style="background:var(--danger);color:#fff;padding:0.15rem 0.5rem;border-radius:6px;font-size:0.75rem;margin-left:0.5rem;">Estourado</span>
                @else
                    <span style="background:#f59e0b;color:#fff;padding:0.15rem 0.5rem;border-radius:6px;font-size:0.75rem;margin-left:0.5rem;">Atenção</span>
                @endif
            </div>
            <span @style(["font-size:0.875rem", "font-weight:600", "color:" . ($alert->alert_type === 'exceeded' ? 'var(--danger)' : '#f59e0b')])>
                {{ number_format($alert->percentage, 0) }}% do orçamento
            </span>
        </div>
    @endforeach
</div>
@endif

<div class="charts-row">
    <!-- Gastos por Categoria -->
    <div class="card">
        <div class="card-header">
            <span class="card-title">Gastos por Categoria</span>
            <a class="card-link" href="{{ route('transactions.index') }}">Ver detalhes →</a>
        </div>
        @if(!empty($expenseCategories))
            @php $maxCat = max(array_map(fn($c) => (float)$c->total, $expenseCategories)); @endphp
            @foreach($expenseCategories as $i => $cat)
                @php
                    $pct = $maxCat > 0 ? round(($cat->total / $maxCat) * 100) : 0;
                    $colors = ['#f87171','#60a5fa','#a78bfa','#f59e0b','#34d399','#ec4899','#14b8a6','#f97316','#6b7280','#84cc16'];
                    $color = $colors[$i % count($colors)];
                @endphp
                <div style="margin-bottom:0.75rem;">
                    <div style="display:flex;justify-content:space-between;font-size:0.8125rem;margin-bottom:0.25rem;">
                        <span>{{ $cat->name }}</span>
                        <span style="font-weight:600;">R$ {{ number_format($cat->total, 2, ',', '.') }}</span>
                    </div>
                    <div class="progress-bar-container">
                        <div class="progress-bar" @style(["width:{$pct}%", "background:{$color}"])></div>
                    </div>
                </div>
            @endforeach
        @else
            <p class="text-muted">Sem despesas este mês.</p>
        @endif
    </div>

    <!-- Metas Ativas -->
    <div class="card">
        <div class="card-header">
            <span class="card-title">Metas Ativas</span>
            <a class="card-link" href="{{ route('goals.index') }}">Ver todas →</a>
        </div>
        @forelse($goals->take(4) as $goal)
            <div style="margin-bottom:0.75rem;">
                <div style="display:flex;justify-content:space-between;font-size:0.8125rem;margin-bottom:0.25rem;">
                    <span>{{ $goal->icon }} {{ $goal->name }}</span>
                    <span style="font-weight:600;">{{ number_format($goal->progressPercentage(), 0) }}%</span>
                </div>
                <div class="progress-bar-container">
                    <div class="progress-bar progress-bar-ok" @style(["width:" . $goal->progressPercentage() . "%"])></div>
                </div>
            </div>
        @empty
            <p class="text-muted">Nenhuma meta ativa.</p>
        @endforelse
    </div>
</div>

<!-- Resumo Dívidas & Orçamentos -->
<div class="charts-row">
    <div class="card">
        <div class="card-header">
            <span class="card-title">Dívidas Ativas</span>
            <a class="card-link" href="{{ route('debts.index') }}">Ver todas →</a>
        </div>
        @if($debts->count() > 0)
            <p style="font-size:1.25rem;font-weight:700;color:var(--danger);margin-bottom:0.75rem;">
                R$ {{ number_format($totalDividas, 2, ',', '.') }} <span style="font-size:0.8125rem;font-weight:400;color:var(--text-secondary);">total restante</span>
            </p>
            @foreach($debts->take(3) as $debt)
                <div style="margin-bottom:0.5rem;">
                    <div style="display:flex;justify-content:space-between;font-size:0.8125rem;">
                        <span>{{ $debt->name }}</span>
                        <span>R$ {{ number_format($debt->remainingBalance(), 2, ',', '.') }}</span>
                    </div>
                </div>
            @endforeach
        @else
            <p class="text-muted">Nenhuma dívida ativa. 🎉</p>
        @endif
    </div>

    <div class="card">
        <div class="card-header">
            <span class="card-title">Orçamentos do Mês</span>
            <a class="card-link" href="{{ route('budgets.index') }}">Ver todos →</a>
        </div>
        @forelse($budgets->take(4) as $budget)
            @php
                $spent = \App\Models\Transaction::forUser(auth()->id())
                    ->where('category_id', $budget->category_id)
                    ->ofType(\App\Enums\TransactionType::Saida)
                    ->forMonth($month)
                    ->sum('amount');
                $pct = $budget->amount > 0 ? round(($spent / $budget->amount) * 100, 1) : 0;
                $barClass = $pct >= 100 ? 'progress-bar-exceeded' : ($pct >= 80 ? 'progress-bar-warning' : 'progress-bar-ok');
            @endphp
            <div style="margin-bottom:0.75rem;">
                <div style="display:flex;justify-content:space-between;font-size:0.8125rem;margin-bottom:0.25rem;">
                    <span>{{ $budget->category->name }}</span>
                    <span style="font-weight:600;">{{ number_format($pct, 0) }}%</span>
                </div>
                <div class="progress-bar-container">
                    <div class="progress-bar {{ $barClass }}" @style(["width:" . min($pct, 100) . "%"])></div>
                </div>
            </div>
        @empty
            <p class="text-muted">Nenhum orçamento definido.</p>
        @endforelse
    </div>
</div>
@endsection
