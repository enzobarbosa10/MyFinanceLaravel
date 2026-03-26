@extends('layouts.app')
@section('title', 'Nova Transação — MyFinance')

@section('content')
<div class="card">
    <h2>Nova Transação</h2>

    @if($errors->any())
        <div class="alert alert-error">{{ $errors->first() }}</div>
    @endif

    @if($accounts->isEmpty())
        <div class="alert alert-error">
            Você precisa <a href="{{ route('accounts.create') }}">criar uma conta</a> antes de registrar transações.
        </div>
    @else
        <form method="POST" action="{{ route('transactions.store') }}">
            @csrf
            <label for="type">Tipo</label>
            <select id="type" name="type" required>
                <option value="">Selecione</option>
                <option value="entrada" {{ old('type') === 'entrada' ? 'selected' : '' }}>Entrada</option>
                <option value="saida" {{ old('type') === 'saida' ? 'selected' : '' }}>Saída</option>
            </select>

            <label for="category_id">Categoria</label>
            <select id="category_id" name="category_id" required>
                <option value="">Selecione o tipo primeiro</option>
            </select>

            <label for="account_id">Conta</label>
            <select id="account_id" name="account_id" required>
                <option value="">Selecione</option>
                @foreach($accounts as $acc)
                    <option value="{{ $acc->id }}" {{ old('account_id') == $acc->id ? 'selected' : '' }}>
                        {{ $acc->name }} (R$ {{ number_format($acc->balance, 2, ',', '.') }})
                    </option>
                @endforeach
            </select>

            <label for="amount">Valor (R$)</label>
            <input type="number" id="amount" name="amount" step="0.01" min="0.01" required value="{{ old('amount') }}">

            <label for="description">Descrição (opcional)</label>
            <input type="text" id="description" name="description" maxlength="255" placeholder="Ex: Almoço, Salário mensal..." value="{{ old('description') }}">

            <label for="transaction_at">Data</label>
            <input type="date" id="transaction_at" name="transaction_at" value="{{ old('transaction_at', date('Y-m-d')) }}" required>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Salvar</button>
                <a href="{{ route('transactions.index') }}" class="btn btn-ghost">Voltar</a>
            </div>
        </form>
    @endif
</div>

@push('scripts')
<script>
    const typeSelect = document.getElementById('type');
    const categorySelect = document.getElementById('category_id');

    typeSelect.addEventListener('change', async function() {
        const type = this.value;
        categorySelect.innerHTML = '<option value="">Carregando...</option>';

        if (!type) {
            categorySelect.innerHTML = '<option value="">Selecione o tipo primeiro</option>';
            return;
        }

        const response = await fetch(`{{ route('categories.byType') }}?type=${type}`);
        const categories = await response.json();

        categorySelect.innerHTML = '<option value="">Selecione</option>';
        categories.forEach(cat => {
            const option = document.createElement('option');
            option.value = cat.id;
            option.textContent = cat.name;
            categorySelect.appendChild(option);
        });
    });
</script>
@endpush
@endsection
