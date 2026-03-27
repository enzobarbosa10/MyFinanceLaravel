<?php

namespace App\Models;

use App\Enums\GoalStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Goal extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'name', 'target_amount', 'current_amount', 'deadline', 'icon', 'status'];

    protected $casts = [
        'status' => GoalStatus::class,
        'target_amount' => 'decimal:2',
        'current_amount' => 'decimal:2',
        'deadline' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function contributions(): HasMany
    {
        return $this->hasMany(GoalContribution::class);
    }

    // ── Scopes ───────────────────────────────────────────────

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', GoalStatus::Active);
    }

    public function scopeCompleted(Builder $query): Builder
    {
        return $query->where('status', GoalStatus::Completed);
    }

    public function scopeCancelled(Builder $query): Builder
    {
        return $query->where('status', GoalStatus::Cancelled);
    }

    public function scopeOverdue(Builder $query): Builder
    {
        return $query->where('status', GoalStatus::Active)
                     ->where('deadline', '<', now());
    }

    // ── Accessors ────────────────────────────────────────────

    public function progressPercentage(): float
    {
        if ($this->target_amount <= 0) {
            return 0;
        }

        return min(100, round(($this->current_amount / $this->target_amount) * 100, 1));
    }

    public function getRemainingAmountAttribute(): float
    {
        return max(0, $this->target_amount - $this->current_amount);
    }

    public function getIsCompletedAttribute(): bool
    {
        return $this->current_amount >= $this->target_amount;
    }

    public function getIsOverdueAttribute(): bool
    {
        return $this->status === GoalStatus::Active && $this->deadline->isPast();
    }
}
