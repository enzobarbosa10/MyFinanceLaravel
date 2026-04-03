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

{{-- Pluggy Connect SDK --}}
<script src="https://cdn.pluggy.ai/connect/v2/pluggy-connect.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    const statusEl  = document.getElementById('syncStatus');

    function showStatus(msg, type = 'success') {
        statusEl.textContent = msg;
        statusEl.className = 'alert alert-' + type;
        statusEl.style.display = 'block';
        if (type !== 'info') {
            setTimeout(() => statusEl.style.display = 'none', 6000);
        }
    }

    async function apiRequest(url, options = {}) {
        const res = await fetch(url, {
            ...options,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                ...(options.headers || {}),
            },
        });

        const data = await res.json().catch(() => null);

        if (!res.ok) {
            const msg = data?.message || data?.errors?.[0]?.message || `Erro ${res.status}. Tente novamente.`;
            throw new Error(msg);
        }

        return data;
    }

    // ── Conectar banco ───────────────────────────────────────
    document.getElementById('btnConnect').addEventListener('click', async function () {
        this.disabled = true;
        this.textContent = 'Carregando...';

        try {
            const data = await apiRequest('{{ route("open-finance.connect-token") }}', {
                method: 'POST',
            });

            const pluggyConnect = new window.PluggyConnect({
                connectToken: data.accessToken,
                includeSandbox: {{ config('app.env') !== 'production' ? 'true' : 'false' }},
                onSuccess: async (itemData) => {
                    showStatus('Conta conectada! Sincronizando dados...', 'info');

                    try {
                        await apiRequest('{{ route("api.open-finance.on-connect") }}', {
                            method: 'POST',
                            body: JSON.stringify({ item_id: itemData.item.id }),
                        });
                        showStatus('Sincronização iniciada! Seus dados aparecerão em instantes.');
                        setTimeout(() => location.reload(), 3000);
                    } catch (e) {
                        showStatus(e.message, 'error');
                    }
                },
                onError: (error) => {
                    console.error('Pluggy Connect error:', error);
                    showStatus('Erro ao conectar. Tente novamente.', 'error');
                },
                onClose: () => {
                    this.disabled = false;
                    this.textContent = '+ Conectar Banco';
                },
            });

            pluggyConnect.init();
        } catch (e) {
            console.error(e);
            showStatus(e.message || 'Erro ao iniciar conexão.', 'error');
            this.disabled = false;
            this.textContent = '+ Conectar Banco';
        }
    });

    // ── Sincronizar ──────────────────────────────────────────
    document.querySelectorAll('.btn-sync').forEach(btn => {
        btn.addEventListener('click', async function () {
            const itemId = this.dataset.itemId;
            this.disabled = true;
            showStatus('Sincronizando...', 'info');

            try {
                await apiRequest('{{ route("api.open-finance.sync") }}', {
                    method: 'POST',
                    body: JSON.stringify({ item_id: itemId }),
                });
                showStatus('Sincronização iniciada! Seus dados serão atualizados em instantes.');
                setTimeout(() => location.reload(), 3000);
            } catch (e) {
                showStatus(e.message || 'Erro ao sincronizar.', 'error');
            }
            this.disabled = false;
        });
    });

    // ── Desconectar ──────────────────────────────────────────
    document.querySelectorAll('.btn-disconnect').forEach(btn => {
        btn.addEventListener('click', async function () {
            if (!confirm('Deseja realmente desconectar esta conta? As transações importadas serão removidas.')) return;

            const itemId = this.dataset.itemId;
            this.disabled = true;

            try {
                await apiRequest('{{ route("api.open-finance.disconnect") }}', {
                    method: 'POST',
                    body: JSON.stringify({ item_id: itemId }),
                });
                showStatus('Conta desconectada.');
                setTimeout(() => location.reload(), 1000);
            } catch (e) {
                showStatus(e.message || 'Erro ao desconectar.', 'error');
            }
            this.disabled = false;
        });
    });
});
</script>
@endsection
