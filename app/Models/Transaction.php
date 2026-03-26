<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

class Transaction extends Model
{
    protected $fillable = [
        'user_id', 'account_id', 'category_id', 'type', 'amount',
        'description', 'transaction_at', 'source', 'raw_description',
        'confidence_score', 'import_id', 'is_recurring',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'transaction_at' => 'datetime',
        'is_recurring' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function import(): BelongsTo
    {
        return $this->belongsTo(Import::class);
    }

    public static function createWithBalance(array $data): self
    {
        return DB::transaction(function () use ($data) {
            $transaction = self::create($data);

            $balanceChange = $data['type'] === 'entrada' ? $data['amount'] : -$data['amount'];
            Account::where('id', $data['account_id'])
                ->where('user_id', $data['user_id'])
                ->increment('balance', $balanceChange);

            return $transaction;
        });
    }

    public static function deleteWithBalance(self $transaction): bool
    {
        return DB::transaction(function () use ($transaction) {
            $balanceRevert = $transaction->type === 'entrada'
                ? -$transaction->amount
                : $transaction->amount;

            Account::where('id', $transaction->account_id)
                ->increment('balance', $balanceRevert);

            return $transaction->delete();
        });
    }

    public static function sumByType(int $userId, string $type, ?string $month = null): float
    {
        $query = self::where('user_id', $userId)->where('type', $type);

        if ($month) {
            $query->whereRaw("DATE_FORMAT(transaction_at, '%Y-%m') = ?", [$month]);
        }

        return (float) $query->sum('amount');
    }

    public static function byMonth(int $userId, string $month): \Illuminate\Database\Eloquent\Collection
    {
        return self::with(['account', 'category'])
            ->where('user_id', $userId)
            ->whereRaw("DATE_FORMAT(transaction_at, '%Y-%m') = ?", [$month])
            ->orderByDesc('transaction_at')
            ->orderByDesc('id')
            ->get();
    }

    public static function summaryByCategory(int $userId, string $month): array
    {
        return DB::select("
            SELECT c.name, c.type, SUM(t.amount) as total
            FROM transactions t
            JOIN categories c ON t.category_id = c.id
            WHERE t.user_id = ? AND DATE_FORMAT(t.transaction_at, '%Y-%m') = ?
            GROUP BY c.id, c.name, c.type
            ORDER BY total DESC
        ", [$userId, $month]);
    }
}
