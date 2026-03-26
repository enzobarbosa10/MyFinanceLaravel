<?php

namespace App\Models;

use App\Enums\TransactionType;
use Illuminate\Database\Eloquent\Builder;
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
        'type' => TransactionType::class,
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

    // ── Query Scopes ─────────────────────────────────────────

    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForMonth(Builder $query, string $month): Builder
    {
        [$year, $mon] = explode('-', $month);

        return $query->whereYear('transaction_at', $year)
                     ->whereMonth('transaction_at', $mon);
    }

    public function scopeOfType(Builder $query, TransactionType $type): Builder
    {
        return $query->where('type', $type);
    }

    // ── Business Methods ─────────────────────────────────────

    public static function createWithBalance(array $data): self
    {
        return DB::transaction(function () use ($data) {
            $transaction = self::create($data);

            $type = $data['type'] instanceof TransactionType
                ? $data['type']
                : TransactionType::from($data['type']);

            $balanceChange = $type === TransactionType::Entrada ? $data['amount'] : -$data['amount'];
            Account::where('id', $data['account_id'])
                ->where('user_id', $data['user_id'])
                ->increment('balance', $balanceChange);

            return $transaction;
        });
    }

    public static function deleteWithBalance(self $transaction): bool
    {
        return DB::transaction(function () use ($transaction) {
            $balanceRevert = $transaction->type === TransactionType::Entrada
                ? -$transaction->amount
                : $transaction->amount;

            Account::where('id', $transaction->account_id)
                ->increment('balance', $balanceRevert);

            return $transaction->delete();
        });
    }

    public static function updateWithBalance(self $transaction, array $data): self
    {
        return DB::transaction(function () use ($transaction, $data) {
            // Revert old balance on old account
            $oldRevert = $transaction->type === TransactionType::Entrada
                ? -$transaction->amount
                : $transaction->amount;
            Account::where('id', $transaction->account_id)
                ->where('user_id', $transaction->user_id)
                ->increment('balance', $oldRevert);

            // Update the transaction
            $transaction->update($data);
            $transaction->refresh();

            // Apply new balance on (possibly new) account
            $newChange = $transaction->type === TransactionType::Entrada
                ? $transaction->amount
                : -$transaction->amount;
            Account::where('id', $transaction->account_id)
                ->where('user_id', $transaction->user_id)
                ->increment('balance', $newChange);

            return $transaction;
        });
    }

    public static function sumByType(int $userId, TransactionType $type, ?string $month = null): float
    {
        $query = self::forUser($userId)->ofType($type);

        if ($month) {
            $query->forMonth($month);
        }

        return (float) $query->sum('amount');
    }

    public static function byMonth(int $userId, string $month, int $perPage = 25): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return self::with(['account', 'category'])
            ->forUser($userId)
            ->forMonth($month)
            ->orderByDesc('transaction_at')
            ->orderByDesc('id')
            ->paginate($perPage)
            ->appends(['month' => $month]);
    }

    public static function summaryByCategory(int $userId, string $month): array
    {
        [$year, $mon] = explode('-', $month);

        return self::join('categories as c', 'transactions.category_id', '=', 'c.id')
            ->where('transactions.user_id', $userId)
            ->whereYear('transactions.transaction_at', $year)
            ->whereMonth('transactions.transaction_at', $mon)
            ->groupBy('c.id', 'c.name', 'c.type')
            ->orderByDesc('total')
            ->select('c.name', 'c.type')
            ->selectRaw('SUM(transactions.amount) as total')
            ->get()
            ->toArray();
    }
}
