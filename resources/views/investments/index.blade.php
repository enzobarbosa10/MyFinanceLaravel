@extends('layouts.app')
@section('title', 'Investimentos — MyFinance')

@section('content')
<div class="card">
    <div class="filter-bar">
        <h2 style="margin-bottom: 0;">Meus Investimentos</h2>
        <div>
            <span style="font-size:1.25rem;font-weight:700;color:var(--blue);margin-right:1rem;">
                Total: R$ {{ number_format($totalInvestido, 2, ',', '.') }}
            </span>
            <a href="{{ route('investments.create') }}" class="btn btn-primary">+ Novo Investimento</a>
        </div>
    </div>

    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Ativo</th>
                    <th>Tipo</th>
                    <th>Símbolo</th>
                    <th>Quantidade</th>
                    <th>Preço Unit.</th>
                    <th>Valor Total</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($investments as $inv)
                    <tr>
                        <td>{{ $inv->asset->name }}</td>
                        <td>{{ $inv->asset->type->name }}</td>
                        <td><strong>{{ $inv->asset->symbol }}</strong></td>
                        <td>{{ number_format($inv->quantity, 4, ',', '.') }}</td>
                        <td>R$ {{ number_format($inv->purchase_price, 4, ',', '.') }}</td>
                        <td class="value-neutral">R$ {{ number_format($inv->total_value, 2, ',', '.') }}</td>
                        <td>
                            <form method="POST" action="{{ route('investments.destroy') }}" onsubmit="return confirm('Remover investimento?')">
                                @csrf
                                <input type="hidden" name="id" value="{{ $inv->id }}">
                                <button type="submit" class="btn btn-danger btn-sm">✕</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-muted">Nenhum investimento cadastrado.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
