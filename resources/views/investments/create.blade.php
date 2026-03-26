@extends('layouts.app')
@section('title', 'Novo Investimento — MyFinance')

@section('content')
<div class="card">
    <h2>Novo Investimento</h2>

    @if($errors->any())
        <div class="alert alert-error">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('investments.store') }}">
        @csrf
        <label for="type_id">Tipo de Investimento</label>
        <select id="type_id" name="type_id" required>
            <option value="">Selecione o tipo</option>
            @foreach($types as $type)
                <option value="{{ $type->id }}">{{ $type->name }}</option>
            @endforeach
        </select>

        <label for="asset_id">Ativo</label>
        <select id="asset_id" name="asset_id" required>
            <option value="">Selecione o tipo primeiro</option>
        </select>

        <label for="quantity">Quantidade</label>
        <input type="number" id="quantity" name="quantity" step="0.0001" min="0.0001" required value="{{ old('quantity') }}">

        <label for="purchase_price">Preço Unitário (R$)</label>
        <input type="number" id="purchase_price" name="purchase_price" step="0.0001" min="0.0001" required value="{{ old('purchase_price') }}">

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Registrar</button>
            <a href="{{ route('investments.index') }}" class="btn btn-ghost">Voltar</a>
        </div>
    </form>
</div>

@push('scripts')
<script>
    const typeSelect = document.getElementById('type_id');
    const assetSelect = document.getElementById('asset_id');

    const assetsByType = @json($types->mapWithKeys(fn($t) => [$t->id => $t->assets]));

    typeSelect.addEventListener('change', function() {
        const typeId = this.value;
        assetSelect.innerHTML = '<option value="">Selecione</option>';

        if (assetsByType[typeId]) {
            assetsByType[typeId].forEach(asset => {
                const option = document.createElement('option');
                option.value = asset.id;
                option.textContent = `${asset.name} (${asset.symbol})`;
                assetSelect.appendChild(option);
            });
        }
    });
</script>
@endpush
@endsection
