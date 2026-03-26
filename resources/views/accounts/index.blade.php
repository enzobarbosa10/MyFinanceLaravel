@extends('layouts.app')
@section('title', 'Minhas Contas — MyFinance')

@section('content')
<div class="card">
    <div class="filter-bar">
        <h2 style="margin-bottom: 0;">Minhas Contas</h2>
        <a href="{{ route('accounts.create') }}" class="btn btn-primary">+ Nova Conta</a>
    </div>

    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Tipo</th>
                    <th>Saldo</th>
                </tr>
            </thead>
            <tbody>
                @forelse($accounts as $account)
                    <tr>
                        <td>{{ $account->name }}</td>
                        <td>{{ ucfirst($account->type) }}</td>
                        <td class="value-neutral">R$ {{ number_format($account->balance, 2, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="text-muted">Nenhuma conta cadastrada.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
