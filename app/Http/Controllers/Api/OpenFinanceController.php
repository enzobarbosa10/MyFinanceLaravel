<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\OpenFinanceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OpenFinanceController extends Controller
{
    public function __construct(
        private OpenFinanceService $openFinance,
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

        return response()->json(['accessToken' => $token]);
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
     * Callback após usuário conectar conta no widget.
     * Despacha sync para a fila e retorna imediatamente.
     */
    public function onConnect(Request $request): JsonResponse
    {
        $request->validate([
            'item_id' => 'required|string',
        ]);

        $user   = $request->user();
        $itemId = $request->input('item_id');

        \App\Jobs\SyncOpenFinanceData::dispatch($user, $itemId);

        return response()->json([
            'message' => 'Sincronização iniciada. Seus dados aparecerão em instantes.',
            'status'  => 'processing',
        ], 202);
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

        \App\Jobs\SyncOpenFinanceData::dispatch($user, $itemId, $from, $to);

        return response()->json([
            'message' => 'Sincronização iniciada.',
            'status'  => 'processing',
        ], 202);
    }

    /**
     * Desconecta uma conta bancária (remove item e limpa dados locais).
     */
    public function disconnect(Request $request): JsonResponse
    {
        $request->validate([
            'item_id' => 'required|string',
        ]);

        $this->openFinance->disconnectItem($request->user(), $request->input('item_id'));

        return response()->json(['message' => 'Conta desconectada com sucesso.']);
    }
}
