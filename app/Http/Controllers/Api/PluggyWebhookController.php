<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\OpenFinanceService;
use App\Services\TransactionCategorizationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PluggyWebhookController extends Controller
{
    public function __construct(
        private OpenFinanceService $openFinance,
        private TransactionCategorizationService $categorization,
    ) {}

    public function handle(Request $request): JsonResponse
    {
        $event = $request->all();

        Log::info('Pluggy webhook received', [
            'event'   => $event['event'] ?? 'unknown',
            'eventId' => $event['eventId'] ?? null,
            'itemId'  => $event['itemId'] ?? null,
        ]);

        match ($event['event'] ?? null) {
            'item/created' => $this->handleItemCreated($event['itemId']),
            'item/updated' => $this->handleItemUpdated($event['itemId']),
            'item/error'   => $this->handleItemError($event['itemId'], $event['error'] ?? null),
            default        => Log::warning('Pluggy webhook: evento desconhecido', $event),
        };

        return response()->json(['received' => true]);
    }

    private function handleItemCreated(string $itemId): void
    {
        Log::info("Pluggy: item criado — {$itemId}");
        $this->syncItem($itemId);
    }

    private function handleItemUpdated(string $itemId): void
    {
        Log::info("Pluggy: item atualizado — {$itemId}");
        $this->syncItem($itemId);
    }

    private function handleItemError(string $itemId, ?array $error): void
    {
        Log::error("Pluggy: erro no item {$itemId}", ['error' => $error]);
    }

    private function syncItem(string $itemId): void
    {
        $item = $this->openFinance->getItem($itemId);
        $clientUserId = $item['clientUserId'] ?? null;

        if (!$clientUserId) {
            Log::warning("Pluggy webhook: clientUserId ausente para item {$itemId}");
            return;
        }

        $user = User::find($clientUserId);

        if (!$user) {
            Log::warning("Pluggy webhook: usuário {$clientUserId} não encontrado");
            return;
        }

        $this->openFinance->syncAccounts($user, $itemId);

        $from = now()->subMonths(3)->toDateString();
        $to   = now()->toDateString();
        $this->openFinance->syncTransactions($user, $itemId, $from, $to);

        $this->categorization->categorizeUncategorized($user);

        Log::info("Pluggy webhook: dados sincronizados para user {$user->id}");
    }
}
