<?php

namespace App\Http\Middleware;

use App\Models\IdempotencyKey;
use App\Support\ApiResponse;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class EnsureIdempotency
{
    public function handle(Request $request, Closure $next): Response
    {
        $idempotencyKey = $request->header('Idempotency-Key');

        if (! $idempotencyKey) {
            return ApiResponse::error('Header Idempotency-Key é obrigatório.', 422);
        }

        $userId = $request->user()?->id;
        $requestHash = $this->makeRequestHash($request);

        $record = DB::transaction(function () use ($userId, $idempotencyKey, $requestHash) {
            $existing = IdempotencyKey::where('user_id', $userId)
                ->where('idempotency_key', $idempotencyKey)
                ->lockForUpdate()
                ->first();

            if (! $existing) {
                return IdempotencyKey::create([
                    'user_id' => $userId,
                    'idempotency_key' => $idempotencyKey,
                    'request_hash' => $requestHash,
                ]);
            }

            return $existing;
        });

        $earlyResponse = $this->resolveEarlyResponse($record, $requestHash);
        if ($earlyResponse) {
            return $earlyResponse;
        }

        /** @var Response $response */
        $response = $next($request);

        $body = $this->extractResponseBody($response);

        $record->update([
            'status_code' => $response->getStatusCode(),
            'response_body' => $body,
            'completed_at' => now(),
        ]);

        return $response;
    }

    private function makeRequestHash(Request $request): string
    {
        $payload = [
            'method' => $request->method(),
            'path' => $request->path(),
            'query' => $request->query(),
            'body' => $request->all(),
        ];

        return hash('sha256', json_encode($payload));
    }

    private function extractResponseBody(Response $response): array
    {
        if ($response instanceof JsonResponse) {
            $data = $response->getData(true);
            return is_array($data) ? $data : ['data' => $data];
        }

        return [
            'data' => $response->getContent(),
            'meta' => [],
            'errors' => null,
        ];
    }

    private function resolveEarlyResponse(IdempotencyKey $record, string $requestHash): ?JsonResponse
    {
        $response = null;

        if ($record->request_hash !== $requestHash) {
            $response = ApiResponse::error('Idempotency-Key já foi utilizado com payload diferente.', 409);
        }

        if (! $response && $record->completed_at && $record->response_body !== null) {
            $response = response()->json($record->response_body, $record->status_code ?? 200);
        }

        if (! $response && ! $record->wasRecentlyCreated && ! $record->completed_at) {
            $response = ApiResponse::error('Requisição idempotente em processamento. Tente novamente em instantes.', 409);
        }

        return $response;
    }
}
