@extends('layouts.app')
@section('title', 'Dívidas — MyFinance')

@section('content')
<div class="card">
    <div class="filter-bar">
        <h2 style="margin-bottom: 0;">Minhas Dívidas</h2>
        <a href="{{ route('debts.create') }}" class="btn btn-primary">+ Nova Dívida</a>
    </div>

    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Credor</th>
                    <th>Total</th>
                    <th>Pago</th>
                    <th>Restante</th>
                    <th>Progresso</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($debts as $debt)
                    <tr>
                        <td>{{ $debt->name }}</td>
                        <td>{{ $debt->creditor ?: '—' }}</td>
                        <td>R$ {{ number_format($debt->total_amount, 2, ',', '.') }}</td>
                        <td class="value-positive">R$ {{ number_format($debt->paid_amount, 2, ',', '.') }}</td>
                        <td class="value-negative">R$ {{ number_format($debt->remaining, 2, ',', '.') }}</td>
                        <td style="min-width:120px;">
                            <div style="display:flex;align-items:center;gap:0.5rem;">
                                <div class="progress-bar-container" style="flex:1;">
                                    <div class="progress-bar progress-bar-ok" style="width:{{ $debt->percentage }}%;"></div>
                                </div>
                                <span style="font-size:0.75rem;font-weight:600;">{{ number_format($debt->percentage, 0) }}%</span>
                            </div>
                        </td>
                        <td>
                            <span class="badge {{ $debt->status === 'paid' ? 'badge-entrada' : 'badge-saida' }}">
                                {{ $debt->status === 'paid' ? 'Paga' : 'Ativa' }}
                            </span>
                        </td>
                        <td>
                            @if($debt->status === 'active')
                                <form method="POST" action="{{ route('debts.pay') }}" style="display:flex;gap:0.25rem;align-items:center;">
                                    @csrf
                                    <input type="hidden" name="debt_id" value="{{ $debt->id }}">
                                    <input type="number" name="amount" step="0.01" min="0.01" required style="width:90px;padding:0.3rem 0.5rem;font-size:0.8125rem;border:1px solid var(--border);border-radius:6px;background:var(--bg-card);color:var(--text-main);">
                                    <button type="submit" class="btn btn-primary btn-sm">Pagar</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="text-muted">Nenhuma dívida cadastrada. 🎉</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
