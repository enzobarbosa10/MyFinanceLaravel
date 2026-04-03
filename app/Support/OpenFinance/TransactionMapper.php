<?php

namespace App\Support\OpenFinance;

use App\Enums\TransactionType;
use Carbon\Carbon;

/**
 * Maps raw Pluggy API transaction data to the local domain schema.
 */
class TransactionMapper
{
    /**
     * Map a single Pluggy transaction to local domain attributes.
     *
     * @param  array  $remote     Raw transaction from Pluggy API
     * @param  int    $userId
     * @param  int    $accountId  Local account ID
     * @return array  Ready for Transaction::updateOrCreate
     */
    public static function fromPluggy(array $remote, int $userId, int $accountId): array
    {
        return [
            'user_id'         => $userId,
            'account_id'      => $accountId,
            'description'     => self::resolveDescription($remote),
            'raw_description' => $remote['descriptionRaw'] ?? null,
            'amount'          => abs((float) ($remote['amount'] ?? 0)),
            'type'            => self::resolveType($remote),
            'transaction_at'  => self::resolveDate($remote),
            'source'          => 'open_finance',
        ];
    }

    /**
     * Extract the unique key fields for idempotent upsert.
     */
    public static function uniqueKey(array $remote, int $accountId): array
    {
        return [
            'open_finance_id' => $remote['id'],
            'account_id'      => $accountId,
        ];
    }

    /**
     * Map Pluggy amount sign to domain TransactionType (entrada/saida).
     */
    private static function resolveType(array $remote): string
    {
        $amount = (float) ($remote['amount'] ?? 0);

        return $amount >= 0
            ? TransactionType::Entrada->value
            : TransactionType::Saida->value;
    }

    /**
     * Map Pluggy 'date' field to 'transaction_at' datetime.
     */
    private static function resolveDate(array $remote): string
    {
        $raw = $remote['date'] ?? now()->toIso8601String();

        return Carbon::parse($raw)->format('Y-m-d H:i:s');
    }

    /**
     * Pick the best description available.
     */
    private static function resolveDescription(array $remote): string
    {
        return $remote['description']
            ?? $remote['descriptionRaw']
            ?? 'Sem descrição';
    }
}
