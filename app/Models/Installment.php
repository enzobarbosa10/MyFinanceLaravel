<?php

namespace App\Models;

use App\Enums\InstallmentStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Installment extends Model
{
    protected $fillable = [
        'user_id', 'description', 'total_amount', 'installment_amount',
        'total_installments', 'paid_installments', 'category_id', 'account_id',
        'start_date', 'status', 'notes',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'installment_amount' => 'decimal:2',
        'total_installments' => 'integer',
        'paid_installments' => 'integer',
        'start_date' => 'date',
        'status' => InstallmentStatus::class,
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

    public function items(): HasMany
    {
        return $this->hasMany(InstallmentItem::class);
    }

    // ── Scopes ───────────────────────────────────────────────

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', InstallmentStatus::Active);
    }

    public function scopeCompleted(Builder $query): Builder
    {
        return $query->where('status', InstallmentStatus::Completed);
    }

    public function scopeCancelled(Builder $query): Builder
    {
        return $query->where('status', InstallmentStatus::Cancelled);
    }

    // ── Accessors ────────────────────────────────────────────

    public function getProgressPercentageAttribute(): float
    {
        if ($this->total_installments <= 0) {
            return 0;
        }

        return round(($this->paid_installments / $this->total_installments) * 100, 1);
    }

    public function getRemainingInstallmentsAttribute(): int
    {
        return max(0, $this->total_installments - $this->paid_installments);
    }

    public function getRemainingAmountAttribute(): float
    {
        return round($this->installment_amount * $this->remaining_installments, 2);
    }

    public function getPaidAmountAttribute(): float
    {
        return round($this->installment_amount * $this->paid_installments, 2);
    }

    public function getIsCompletedAttribute(): bool
    {
        return $this->paid_installments >= $this->total_installments;
    }

    public function getFormattedProgressAttribute(): string
    {
        return "{$this->paid_installments}/{$this->total_installments}";
    }
}
