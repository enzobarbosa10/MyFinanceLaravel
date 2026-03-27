<?php

namespace App\Models;

use App\Enums\SubscriptionFrequency;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Subscription extends Model
{
    protected $fillable = [
        'user_id', 'name', 'description', 'amount', 'category_id', 'account_id',
        'frequency', 'billing_day', 'start_date', 'end_date', 'next_billing_date',
        'is_active', 'auto_create_transaction', 'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'frequency' => SubscriptionFrequency::class,
        'billing_day' => 'integer',
        'start_date' => 'date',
        'end_date' => 'date',
        'next_billing_date' => 'date',
        'is_active' => 'boolean',
        'auto_create_transaction' => 'boolean',
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

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    // ── Scopes ───────────────────────────────────────────────

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeInactive(Builder $query): Builder
    {
        return $query->where('is_active', false);
    }

    public function scopeDueBefore(Builder $query, string|\DateTimeInterface $date): Builder
    {
        return $query->where('next_billing_date', '<=', $date);
    }

    public function scopeDueToday(Builder $query): Builder
    {
        return $query->whereDate('next_billing_date', today());
    }

    public function scopeAutoCreatable(Builder $query): Builder
    {
        return $query->where('auto_create_transaction', true)
                     ->where('is_active', true);
    }

    // ── Accessors ────────────────────────────────────────────

    public function getFormattedAmountAttribute(): string
    {
        return 'R$ ' . number_format($this->amount, 2, ',', '.');
    }

    public function getIsOverdueAttribute(): bool
    {
        return $this->is_active && $this->next_billing_date->isPast();
    }

    public function getYearlyCostAttribute(): float
    {
        return match ($this->frequency) {
            SubscriptionFrequency::Weekly => $this->amount * 52,
            SubscriptionFrequency::Biweekly => $this->amount * 26,
            SubscriptionFrequency::Monthly => $this->amount * 12,
            SubscriptionFrequency::Quarterly => $this->amount * 4,
            SubscriptionFrequency::Semiannual => $this->amount * 2,
            SubscriptionFrequency::Yearly => $this->amount,
        };
    }

    public function getMonthlyCostAttribute(): float
    {
        return round($this->yearly_cost / 12, 2);
    }
}
