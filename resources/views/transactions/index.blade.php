@extends('layouts.app')
@section('title', 'Transações — MyFinance')

@section('content')
<div class="card">
    <div class="filter-bar">
        <h2 style="margin-bottom: 0;">Transações</h2>
        <form method="GET" action="{{ route('transactions.index') }}">
            <label for="month">Mês:</label>
            <input type="month" id="month" name="month" value="{{ $month }}">
            <button type="submit" class="btn btn-ghost btn-sm">Filtrar</button>
        </form>
    </div>

    <div class="summary-row">
        <div class="summary-card">
            <span class="summary-label">Entradas</span>
            <span class="summary-value value-positive">R$ {{ number_format($entradas, 2, ',', '.') }}</span>
        </div>
        <div class="summary-card">
            <span class="summary-label">Saídas</span>
            <span class="summary-value value-negative">R$ {{ number_format($saidas, 2, ',', '.') }}</span>
        </div>
        <div class="summary-card">
            <span class="summary-label">Saldo do Mês</span>
            <span class="summary-value {{ $saldo >= 0 ? 'value-positive' : 'value-negative' }}">R$ {{ number_format($saldo, 2, ',', '.') }}</span>
        </div>
    </div>

    <div style="margin-top: 1.25rem;">
        <a href="{{ route('transactions.create') }}" class="btn btn-primary">+ Nova Transação</a>
    </div>

    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Data</th>
                    <th>Tipo</th>
                    <th>Categoria</th>
                    <th>Conta</th>
                    <th>Descrição</th>
                    <th>Valor</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($transactions as $t)
                    <tr>
                        <td>{{ $t->transaction_at->format('d/m/Y') }}</td>
                        <td>
                            <span class="badge {{ $t->type === 'entrada' ? 'badge-entrada' : 'badge-saida' }}">
                                {!! $t->type === 'entrada' ? '&#8593; Entrada' : '&#8595; Saída' !!}
                            </span>
                        </td>
                        <td>{{ $t->category->name }}</td>
                        <td>{{ $t->account->name }}</td>
                        <td>{{ $t->description ?: '—' }}</td>
                        <td class="{{ $t->type === 'entrada' ? 'value-positive' : 'value-negative' }}">
                            R$ {{ number_format($t->amount, 2, ',', '.') }}
                        </td>
                        <td>
                            <form method="POST" action="{{ route('transactions.destroy') }}" style="margin:0;" onsubmit="return confirm('Excluir esta transação?')">
                                @csrf
                                <input type="hidden" name="id" value="{{ $t->id }}">
                                <button type="submit" class="btn btn-danger btn-sm">✕</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-muted">Nenhuma transação neste mês.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
