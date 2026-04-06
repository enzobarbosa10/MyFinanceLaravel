document.addEventListener('DOMContentLoaded', function () {
    const config = JSON.parse(document.getElementById('open-finance-config').textContent);
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    const statusEl = document.getElementById('syncStatus');

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
            const data = await apiRequest(config.routes.connectToken, {
                method: 'POST',
            });

            const pluggyConnect = new window.PluggyConnect({
                connectToken: data.accessToken,
                includeSandbox: config.includeSandbox,
                onSuccess: async (itemData) => {
                    showStatus('Conta conectada! Sincronizando dados...', 'info');

                    try {
                        await apiRequest(config.routes.onConnect, {
                            method: 'POST',
                            body: JSON.stringify({
                                item_id: itemData.item.id
                            }),
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
                await apiRequest(config.routes.sync, {
                    method: 'POST',
                    body: JSON.stringify({
                        item_id: itemId
                    }),
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
                await apiRequest(config.routes.disconnect, {
                    method: 'POST',
                    body: JSON.stringify({
                        item_id: itemId
                    }),
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
