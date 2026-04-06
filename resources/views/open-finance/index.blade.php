@extends('layouts.app')

@section('title', 'Open Finance — MyFinance')

@section('content')
<div class="page-header" style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;">
    <h1 style="font-size:1.5rem;font-weight:700;">🏦 Open Finance</h1>
    <button id="btnConnect" class="btn btn-primary">+ Conectar Banco</button>
</div>

{{-- Contas vinculadas --}}
@if($linkedAccounts->isEmpty())
<div class="card" style="padding:2rem;text-align:center;">
    <p style="font-size:1.1rem;color:var(--text-secondary);margin-bottom:1rem;">
        Nenhuma conta bancária conectada via Open Finance.
    </p>
    <p style="color:var(--text-secondary);font-size:0.9rem;">
        Clique em <strong>"+ Conectar Banco"</strong> para vincular suas contas automaticamente.
    </p>
</div>
@else
<div class="card" style="padding:0;overflow:hidden;">
    <table style="width:100%;border-collapse:collapse;">
        <thead>
            <tr style="background:var(--bg-secondary);text-align:left;">
                <th style="padding:0.75rem 1rem;font-weight:600;">Conta</th>
                <th style="padding:0.75rem 1rem;font-weight:600;">Tipo</th>
                <th style="padding:0.75rem 1rem;font-weight:600;text-align:right;">Saldo</th>
                <th style="padding:0.75rem 1rem;font-weight:600;text-align:center;">Ações</th>
            </tr>
        </thead>
        <tbody>
            @foreach($linkedAccounts as $account)
            <tr style="border-top:1px solid var(--border-color);">
                <td style="padding:0.75rem 1rem;">{{ $account->name }}</td>
                <td style="padding:0.75rem 1rem;">
                    <span class="badge">{{ ucfirst($account->type) }}</span>
                </td>
                <td style="padding:0.75rem 1rem;text-align:right;font-weight:600;">
                    {{ $account->formatted_balance }}
                </td>
                <td style="padding:0.75rem 1rem;text-align:center;">
                    <button class="btn btn-sm btn-outline btn-sync"
                        data-item-id="{{ $account->open_finance_item_id }}"
                        title="Sincronizar">
                        🔄
                    </button>
                    <button class="btn btn-sm btn-danger btn-disconnect"
                        data-item-id="{{ $account->open_finance_item_id }}"
                        title="Desconectar">
                        ✕
                    </button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif

{{-- Status de sincronização --}}
<div id="syncStatus" style="display:none;margin-top:1rem;" class="alert alert-success"></div>

{{-- Config bridge: PHP → JS --}}
<script id="open-finance-config" type="application/json">
    @json([
        'routes' => [
            'connectToken' => route('open-finance.connect-token'),
            'onConnect'    => route('api.open-finance.on-connect'),
            'sync'         => route('api.open-finance.sync'),
            'disconnect'   => route('api.open-finance.disconnect'),
        ],
        'includeSandbox' => config('app.env') !== 'production',
    ])
</script>

{{-- Pluggy Connect SDK --}}
<script src="https://cdn.pluggy.ai/connect/v2/pluggy-connect.js"></script>
<script src="{{ asset('js/open-finance.js') }}"></script>
@endsection
