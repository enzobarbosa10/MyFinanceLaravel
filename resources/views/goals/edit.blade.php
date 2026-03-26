@extends('layouts.app')
@section('title', 'Editar Meta — MyFinance')

@section('content')
<div class="card">
    <h2>Editar Meta</h2>

    @if($errors->any())
        <div class="alert alert-error">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('goals.update', $goal) }}">
        @csrf
        @method('PUT')

        <label for="name">Nome da Meta</label>
        <input type="text" id="name" name="name" required value="{{ old('name', $goal->name) }}" placeholder="Ex: Viagem, Reserva de emergência...">

        <label for="icon">Ícone (emoji)</label>
        <input type="text" id="icon" name="icon" value="{{ old('icon', $goal->icon) }}" maxlength="10">

        <label for="target_amount">Valor Alvo (R$)</label>
        <input type="number" id="target_amount" name="target_amount" step="0.01" min="1" required value="{{ old('target_amount', $goal->target_amount) }}">

        <label for="deadline">Prazo</label>
        <input type="date" id="deadline" name="deadline" required value="{{ old('deadline', $goal->deadline->format('Y-m-d')) }}">

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Atualizar Meta</button>
            <a href="{{ route('goals.index') }}" class="btn btn-ghost">Voltar</a>
        </div>
    </form>
</div>
@endsection
