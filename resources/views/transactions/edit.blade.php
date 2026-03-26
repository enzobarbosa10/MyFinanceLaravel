@extends('layouts.app')
@section('title', 'Editar Transação — MyFinance')
@use(App\Enums\TransactionType)

@section('content')
<div class="card">
    <h2>Editar Transação</h2>

    @if($errors->any())
        <div class="alert alert-error">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('transactions.update', $transaction) }}">
        @csrf
        @method('PUT')

        <label for="type">Tipo</label>
        <select id="type" name="type" required>
            <option value="">Selecione</option>
            <option value="{{ TransactionType::Entrada->value }}" {{ old('type', $transaction->type->value) === TransactionType::Entrada->value ? 'selected' : '' }}>Entrada</option>
            <option value="{{ TransactionType::Saida->value }}" {{ old('type', $transaction->type->value) === TransactionType::Saida->value ? 'selected' : '' }}>Saída</option>
        </select>

        <label for="category_id">Categoria</label>
        <select id="category_id" name="category_id" required>
            <option value="">Carregando...</option>
        </select>

        <label for="account_id">Conta</label>
        <select id="account_id" name="account_id" required>
            <option value="">Selecione</option>
            @foreach($accounts as $acc)
                <option value="{{ $acc->id }}" {{ old('account_id', $transaction->account_id) == $acc->id ? 'selected' : '' }}>
                    {{ $acc->name }} (R$ {{ number_format($acc->balance, 2, ',', '.') }})
                </option>
            @endforeach
        </select>

        <label for="amount">Valor (R$)</label>
        <input type="number" id="amount" name="amount" step="0.01" min="0.01" required value="{{ old('amount', $transaction->amount) }}">

        <label for="description">Descrição (opcional)</label>
        <input type="text" id="description" name="description" maxlength="255" placeholder="Ex: Almoço, Salário mensal..." value="{{ old('description', $transaction->description) }}">

        <label for="transaction_at">Data</label>
        <input type="date" id="transaction_at" name="transaction_at" value="{{ old('transaction_at', $transaction->transaction_at->format('Y-m-d')) }}" required>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Atualizar</button>
            <a href="{{ route('transactions.index') }}" class="btn btn-ghost">Voltar</a>
        </div>
    </form>
</div>

@push('scripts')
<script>
    const typeSelect = document.getElementById('type');
    const categorySelect = document.getElementById('category_id');
    const currentCategoryId = {{ old('category_id', $transaction->category_id) }};

    async function loadCategories(type, selectedId) {
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
            if (cat.id === selectedId) option.selected = true;
            categorySelect.appendChild(option);
        });
    }

    typeSelect.addEventListener('change', function() {
        loadCategories(this.value, null);
    });

    // Load categories on page load with the current type/category
    loadCategories(typeSelect.value, currentCategoryId);
</script>
@endpush
@endsection
