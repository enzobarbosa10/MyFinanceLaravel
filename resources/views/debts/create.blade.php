@extends('layouts.app')
@section('title', 'Nova Dívida — MyFinance')

@section('content')
<div class="card">
    <h2>Registrar Dívida</h2>

    @if($errors->any())
        <div class="alert alert-error">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('debts.store') }}">
        @csrf
        <label for="name">Nome da Dívida</label>
        <input type="text" id="name" name="name" required value="{{ old('name') }}" placeholder="Ex: Cartão de crédito, Empréstimo...">

        <label for="total_amount">Valor Total (R$)</label>
        <input type="number" id="total_amount" name="total_amount" step="0.01" min="0.01" required value="{{ old('total_amount') }}">

        <label for="monthly_interest_rate">Taxa de Juros Mensal (ex: 0.0199 = 1.99%)</label>
        <input type="number" id="monthly_interest_rate" name="monthly_interest_rate" step="0.0001" min="0" value="{{ old('monthly_interest_rate', '0') }}">

        <label for="min_payment">Parcela Mínima (R$)</label>
        <input type="number" id="min_payment" name="min_payment" step="0.01" min="0" value="{{ old('min_payment', '0') }}">

        <label for="due_day">Dia de Vencimento</label>
        <input type="number" id="due_day" name="due_day" min="1" max="31" required value="{{ old('due_day', '1') }}">

        <label for="creditor">Credor (opcional)</label>
        <input type="text" id="creditor" name="creditor" maxlength="100" value="{{ old('creditor') }}" placeholder="Ex: Banco X, Loja Y...">

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Registrar</button>
            <a href="{{ route('debts.index') }}" class="btn btn-ghost">Voltar</a>
        </div>
    </form>
</div>
@endsection
