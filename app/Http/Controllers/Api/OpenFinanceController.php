<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\OpenFinanceService;
use App\Services\TransactionCategorizationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OpenFinanceController extends Controller
{
    public function __construct(
        private OpenFinanceService $openFinance,
        private TransactionCategorizationService $categorization,
    ) {}

    /**
     * Gera token para o widget de conexão bancária.
     */
    public function connectToken(Request $request): JsonResponse
    {
        $request->validate([
            'item_id' => 'nullable|string',
        ]);

        $token = $this->openFinance->createConnectToken(
            $request->user(),
            $request->input('item_id'),
        );

        return response()->json(['connect_token' => $token]);
    }

    /**
     * Lista conectores (bancos) disponíveis.
     */
    public function connectors(Request $request): JsonResponse
    {
        $connectors = $this->openFinance->getConnectors(
            $request->query('search', ''),
        );

        return response()->json(['connectors' => $connectors]);
    }

    /**
     * Webhook / callback após usuário conectar conta no widget.
     * Sincroniza contas e transações.
     */
    public function onConnect(Request $request): JsonResponse
    {
        $request->validate([
            'item_id' => 'required|string',
        ]);

        $user   = $request->user();
        $itemId = $request->input('item_id');

        $accountsSynced = $this->openFinance->syncAccounts($user, $itemId);

        $from = now()->subMonths(3)->toDateString();
        $to   = now()->toDateString();
        $transactionsSynced = $this->openFinance->syncTransactions($user, $itemId, $from, $to);

        // Categorizar automaticamente as transações importadas
        $this->categorization->categorizeUncategorized($user);

        return response()->json([
            'message'      => 'Conta conectada e dados sincronizados.',
            'accounts'     => $accountsSynced,
            'transactions' => $transactionsSynced,
        ]);
    }

    /**
     * Re-sincroniza dados de um item existente.
     */
    public function sync(Request $request): JsonResponse
    {
        $request->validate([
            'item_id' => 'required|string',
            'from'    => 'nullable|date',
            'to'      => 'nullable|date',
        ]);

        $user   = $request->user();
        $itemId = $request->input('item_id');
        $from   = $request->input('from', now()->subMonth()->toDateString());
        $to     = $request->input('to', now()->toDateString());

        $accountsSynced     = $this->openFinance->syncAccounts($user, $itemId);
        $transactionsSynced = $this->openFinance->syncTransactions($user, $itemId, $from, $to);

        $this->categorization->categorizeUncategorized($user);

        return response()->json([
            'message'      => 'Dados sincronizados com sucesso.',
            'accounts'     => $accountsSynced,
            'transactions' => $transactionsSynced,
        ]);
    }

    /**
     * Desconecta uma conta bancária (remove item).
     */
    public function disconnect(Request $request): JsonResponse
    {
        $request->validate([
            'item_id' => 'required|string',
        ]);

        $this->openFinance->deleteItem($request->input('item_id'));

        return response()->json(['message' => 'Conta desconectada com sucesso.']);
    }
}
