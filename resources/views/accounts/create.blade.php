@extends('layouts.app')
@section('title', 'Criar Conta — MyFinance')

@section('content')
<div class="card">
    <h2>Criar Conta</h2>

    @if($errors->any())
        <div class="alert alert-error">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('accounts.store') }}">
        @csrf
        <label for="name">Nome da Conta</label>
        <input type="text" id="name" name="name" required autofocus value="{{ old('name') }}">

        <label for="balance">Saldo Inicial</label>
        <input type="number" id="balance" step="0.01" name="balance" value="{{ old('balance', '0.00') }}" required>

        <label for="type">Tipo</label>
        <select id="type" name="type">
            <option value="corrente" {{ old('type') === 'corrente' ? 'selected' : '' }}>Corrente</option>
            <option value="poupanca" {{ old('type') === 'poupanca' ? 'selected' : '' }}>Poupança</option>
            <option value="dinheiro" {{ old('type') === 'dinheiro' ? 'selected' : '' }}>Dinheiro</option>
            <option value="outro" {{ old('type') === 'outro' ? 'selected' : '' }}>Outro</option>
        </select>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Criar</button>
            <a href="{{ route('accounts.index') }}" class="btn btn-ghost">Voltar</a>
        </div>
    </form>
</div>
@endsection
