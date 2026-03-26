@extends('layouts.app')
@section('title', 'Novo Orçamento — MyFinance')

@section('content')
<div class="card">
    <h2>Novo Orçamento</h2>

    @if($errors->any())
        <div class="alert alert-error">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('budgets.store') }}">
        @csrf
        <label for="category_id">Categoria (despesa)</label>
        <select id="category_id" name="category_id" required>
            <option value="">Selecione</option>
            @foreach($categories as $cat)
                <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
            @endforeach
        </select>

        <label for="amount">Valor Limite (R$)</label>
        <input type="number" id="amount" name="amount" step="0.01" min="0.01" required value="{{ old('amount') }}">

        <label for="month">Mês</label>
        <input type="month" id="month" name="month" value="{{ old('month', date('Y-m')) }}" required>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Salvar</button>
            <a href="{{ route('budgets.index') }}" class="btn btn-ghost">Voltar</a>
        </div>
    </form>
</div>
@endsection
