<?php

namespace App\Models;

use App\Enums\TransactionType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CategorizationRule extends Model
{
    protected $fillable = [
        'user_id', 'category_id', 'category_name', 'pattern', 'type',
        'source', 'priority', 'is_active',
    ];

    protected $casts = [
        'type'      => TransactionType::class,
        'is_active' => 'boolean',
        'priority'  => 'integer',
    ];

    // ── Relationships ────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    // ── Scopes ───────────────────────────────────────────────

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeSystem(Builder $query): Builder
    {
        return $query->whereNull('user_id');
    }

    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->where(function (Builder $q) use ($userId) {
            $q->whereNull('user_id')
              ->orWhere('user_id', $userId);
        });
    }

    public function scopeByPriority(Builder $query): Builder
    {
        return $query->orderByDesc('priority')->orderBy('id');
    }

    /**
     * Check if the given description matches this rule's pattern.
     */
    public function matches(string $description): bool
    {
        return str_contains(
            mb_strtoupper($description),
            mb_strtoupper($this->pattern)
        );
    }
}
