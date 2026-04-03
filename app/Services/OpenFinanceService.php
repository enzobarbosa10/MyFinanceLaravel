<?php

namespace App\Services;

use App\Models\Account;
use App\Models\Transaction;
use App\Models\User;
use App\Support\OpenFinance\TransactionMapper;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
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
            ->timeout(30)
            ->retry(2, 500)
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
        $response = $this->http()->delete("/items/{$itemId}");

        if ($response->failed()) {
            Log::error("Pluggy delete item failed", ['itemId' => $itemId, 'body' => $response->body()]);
        }
    }

    /**
     * Remove local accounts and transactions linked to the given item,
     * then deletes the item from the Pluggy provider.
     */
    public function disconnectItem(User $user, string $itemId): void
    {
        DB::transaction(function () use ($user, $itemId) {
            $accountIds = Account::where('user_id', $user->id)
                ->where('open_finance_item_id', $itemId)
                ->pluck('id');

            if ($accountIds->isNotEmpty()) {
                Transaction::where('user_id', $user->id)
                    ->whereIn('account_id', $accountIds)
                    ->whereNotNull('open_finance_id')
                    ->delete();

                Account::where('user_id', $user->id)
                    ->where('open_finance_item_id', $itemId)
                    ->update([
                        'open_finance_id'      => null,
                        'open_finance_item_id' => null,
                    ]);
            }
        });

        $this->deleteItem($itemId);

        Log::info("Open Finance item disconnected", [
            'user_id' => $user->id,
            'item_id' => $itemId,
        ]);
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

    /**
     * Sync transactions from Pluggy to local database.
     * Uses TransactionMapper for data adaptation and updateOrCreate for idempotency.
     */
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

            try {
                $remoteTransactions = $this->getTransactions($account->open_finance_id, $from, $to);
            } catch (\Throwable $e) {
                Log::error('Failed to fetch transactions from Pluggy', [
                    'account_id'      => $account->id,
                    'open_finance_id' => $account->open_finance_id,
                    'error'           => $e->getMessage(),
                ]);
                continue;
            }

            foreach ($remoteTransactions as $remote) {
                try {
                    $uniqueKey  = TransactionMapper::uniqueKey($remote, $account->id);
                    $attributes = TransactionMapper::fromPluggy($remote, $user->id, $account->id);

                    Transaction::updateOrCreate($uniqueKey, $attributes);
                    $synced++;
                } catch (\Throwable $e) {
                    Log::error('Failed to upsert transaction', [
                        'open_finance_id' => $remote['id'] ?? null,
                        'account_id'      => $account->id,
                        'error'           => $e->getMessage(),
                    ]);
                }
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
