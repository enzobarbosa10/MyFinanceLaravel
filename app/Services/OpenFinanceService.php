<?php

namespace App\Services;

use App\Models\Account;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenFinanceService
{
    private string $baseUrl;
    private string $clientId;
    private string $clientSecret;

    public function __construct()
    {
        $this->baseUrl      = config('services.pluggy.base_url');
        $this->clientId     = config('services.pluggy.client_id');
        $this->clientSecret = config('services.pluggy.client_secret');
    }

    // ── Autenticação ─────────────────────────────────────────

    private function getAccessToken(): string
    {
        return Cache::remember('pluggy_access_token', 3500, function () {
            $response = Http::post("{$this->baseUrl}/auth", [
                'clientId'     => $this->clientId,
                'clientSecret' => $this->clientSecret,
            ]);

            if ($response->failed()) {
                Log::error('Pluggy auth failed', ['body' => $response->body()]);
                throw new \RuntimeException('Falha na autenticação com Pluggy.');
            }

            return $response->json('apiKey');
        });
    }

    private function http()
    {
        return Http::withToken($this->getAccessToken())
            ->baseUrl($this->baseUrl)
            ->acceptJson();
    }

    // ── Connect Token (widget de conexão) ────────────────────

    public function createConnectToken(User $user, ?string $itemId = null): string
    {
        $payload = ['clientUserId' => (string) $user->id];

        if ($itemId) {
            $payload['itemId'] = $itemId;
        }

        $response = $this->http()->post('/connect_token', $payload);

        if ($response->failed()) {
            Log::error('Pluggy connect token failed', ['body' => $response->body()]);
            throw new \RuntimeException('Falha ao criar token de conexão.');
        }

        return $response->json('accessToken');
    }

    // ── Listar conectores disponíveis ────────────────────────

    public function getConnectors(string $search = ''): array
    {
        $query = ['sandbox' => config('app.env') !== 'production'];

        if ($search) {
            $query['name'] = $search;
        }

        $response = $this->http()->get('/connectors', $query);

        return $response->json('results', []);
    }

    // ── Items (conexões bancárias) ───────────────────────────

    public function getItem(string $itemId): array
    {
        $response = $this->http()->get("/items/{$itemId}");

        if ($response->failed()) {
            throw new \RuntimeException('Falha ao buscar item.');
        }

        return $response->json();
    }

    public function deleteItem(string $itemId): void
    {
        $this->http()->delete("/items/{$itemId}");
    }

    // ── Contas bancárias ─────────────────────────────────────

    public function getAccounts(string $itemId): array
    {
        $response = $this->http()->get('/accounts', [
            'itemId' => $itemId,
        ]);

        return $response->json('results', []);
    }

    // ── Transações ───────────────────────────────────────────

    public function getTransactions(string $accountId, string $from, string $to): array
    {
        $response = $this->http()->get('/transactions', [
            'accountId' => $accountId,
            'from'      => $from,
            'to'        => $to,
        ]);

        return $response->json('results', []);
    }

    // ── Sincronizar dados para o sistema local ───────────────

    public function syncAccounts(User $user, string $itemId): int
    {
        $remoteAccounts = $this->getAccounts($itemId);
        $synced = 0;

        foreach ($remoteAccounts as $remote) {
            Account::updateOrCreate(
                [
                    'user_id'             => $user->id,
                    'open_finance_id'     => $remote['id'],
                ],
                [
                    'name'                => $remote['name'],
                    'type'                => $this->mapAccountType($remote['type']),
                    'balance'             => $remote['balance'] ?? 0,
                    'open_finance_item_id' => $itemId,
                ]
            );
            $synced++;
        }

        return $synced;
    }

    public function syncTransactions(User $user, string $itemId, string $from, string $to): int
    {
        $accounts = Account::where('user_id', $user->id)
            ->where('open_finance_item_id', $itemId)
            ->get();

        $synced = 0;

        foreach ($accounts as $account) {
            if (!$account->open_finance_id) {
                continue;
            }

            $remoteTransactions = $this->getTransactions($account->open_finance_id, $from, $to);

            foreach ($remoteTransactions as $remote) {
                Transaction::updateOrCreate(
                    [
                        'open_finance_id' => $remote['id'],
                        'account_id'      => $account->id,
                    ],
                    [
                        'user_id'     => $user->id,
                        'description' => $remote['description'] ?? $remote['descriptionRaw'] ?? 'Sem descrição',
                        'amount'      => abs($remote['amount']),
                        'type'        => $remote['amount'] < 0 ? 'expense' : 'income',
                        'date'        => $remote['date'],
                        'category_id' => null, // será categorizado pelo TransactionCategorizationService
                    ]
                );
                $synced++;
            }
        }

        return $synced;
    }

    // ── Helpers ──────────────────────────────────────────────

    private function mapAccountType(string $pluggyType): string
    {
        return match ($pluggyType) {
            'BANK'            => 'checking',
            'CREDIT'          => 'credit_card',
            'INVESTMENT'      => 'investment',
            'SAVINGS_ACCOUNT' => 'savings',
            default           => 'other',
        };
    }
}
