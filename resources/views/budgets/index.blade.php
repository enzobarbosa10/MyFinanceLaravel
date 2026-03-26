@extends('layouts.app')
@section('title', 'Orçamentos — MyFinance')

@section('content')
<div class="card">
    <div class="filter-bar">
        <h2 style="margin-bottom: 0;">Orçamentos — {{ \Carbon\Carbon::createFromFormat('Y-m', $month)->translatedFormat('F Y') }}</h2>
        <a href="{{ route('budgets.create') }}" class="btn btn-primary">+ Novo Orçamento</a>
    </div>

    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Categoria</th>
                    <th>Orçamento</th>
                    <th>Gasto</th>
                    <th>Progresso</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($budgets as $budget)
                    @php
                        $barClass = $budget->percentage >= 100 ? 'progress-bar-exceeded'
                            : ($budget->percentage >= 80 ? 'progress-bar-warning' : 'progress-bar-ok');
                    @endphp
                    <tr>
                        <td>{{ $budget->category->name }}</td>
                        <td>R$ {{ number_format($budget->amount, 2, ',', '.') }}</td>
                        <td class="{{ $budget->percentage >= 100 ? 'value-negative' : '' }}">
                            R$ {{ number_format($budget->spent, 2, ',', '.') }}
                        </td>
                        <td style="min-width:150px;">
                            <div style="display:flex;align-items:center;gap:0.5rem;">
                                <div class="progress-bar-container" style="flex:1;">
                                    <div class="progress-bar {{ $barClass }}" style="width:{{ min($budget->percentage, 100) }}%;"></div>
                                </div>
                                <span style="font-size:0.8125rem;font-weight:600;white-space:nowrap;">{{ number_format($budget->percentage, 0) }}%</span>
                            </div>
                        </td>
                        <td>
                            <form method="POST" action="{{ route('budgets.destroy', $budget) }}" onsubmit="return confirm('Remover orçamento?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">✕</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-muted">Nenhum orçamento definido para este mês.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
