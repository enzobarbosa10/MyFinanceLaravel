@extends('layouts.app')
@section('title', 'Nova Meta — MyFinance')

@section('content')
<div class="card">
    <h2>Nova Meta</h2>

    @if($errors->any())
        <div class="alert alert-error">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('goals.store') }}">
        @csrf
        <label for="name">Nome da Meta</label>
        <input type="text" id="name" name="name" required value="{{ old('name') }}" placeholder="Ex: Viagem, Reserva de emergência...">

        <label for="icon">Ícone (emoji)</label>
        <input type="text" id="icon" name="icon" value="{{ old('icon', '🎯') }}" maxlength="10">

        <label for="target_amount">Valor Alvo (R$)</label>
        <input type="number" id="target_amount" name="target_amount" step="0.01" min="1" required value="{{ old('target_amount') }}">

        <label for="deadline">Prazo</label>
        <input type="date" id="deadline" name="deadline" required value="{{ old('deadline') }}">

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Criar Meta</button>
            <a href="{{ route('goals.index') }}" class="btn btn-ghost">Voltar</a>
        </div>
    </form>
</div>
@endsection
