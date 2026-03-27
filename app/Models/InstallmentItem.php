<?php

namespace App\Models;

use App\Enums\InstallmentItemStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InstallmentItem extends Model
{
    protected $fillable = [
        'installment_id', 'installment_number', 'amount',
        'due_date', 'paid_date', 'transaction_id', 'status',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'installment_number' => 'integer',
        'due_date' => 'date',
        'paid_date' => 'date',
        'status' => InstallmentItemStatus::class,
    ];

    // ── Relationships ────────────────────────────────────────

    public function installment(): BelongsTo
    {
        return $this->belongsTo(Installment::class);
    }

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }

    // ── Scopes ───────────────────────────────────────────────

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', InstallmentItemStatus::Pending);
    }

    public function scopePaid(Builder $query): Builder
    {
        return $query->where('status', InstallmentItemStatus::Paid);
    }

    public function scopeOverdue(Builder $query): Builder
    {
        return $query->where('status', InstallmentItemStatus::Overdue);
    }

    public function scopeDueBefore(Builder $query, string|\DateTimeInterface $date): Builder
    {
        return $query->where('due_date', '<=', $date);
    }

    // ── Accessors ────────────────────────────────────────────

    public function getIsPaidAttribute(): bool
    {
        return $this->status === InstallmentItemStatus::Paid;
    }

    public function getIsOverdueAttribute(): bool
    {
        return $this->status !== InstallmentItemStatus::Paid && $this->due_date->isPast();
    }

    public function getFormattedAmountAttribute(): string
    {
        return 'R$ ' . number_format($this->amount, 2, ',', '.');
    }
}
