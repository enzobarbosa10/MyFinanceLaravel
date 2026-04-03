<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserSubscription extends Model
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_ACTIVE = 'active';
    public const STATUS_TRIALING = 'trialing';
    public const STATUS_PAST_DUE = 'past_due';
    public const STATUS_CANCELED = 'canceled';
    public const STATUS_EXPIRED = 'expired';

    public const OPEN_STATUSES = [
        self::STATUS_PENDING,
        self::STATUS_ACTIVE,
        self::STATUS_TRIALING,
        self::STATUS_PAST_DUE,
    ];

    public const ALLOWED_TRANSITIONS = [
        self::STATUS_PENDING => [self::STATUS_ACTIVE, self::STATUS_PAST_DUE, self::STATUS_CANCELED],
        self::STATUS_TRIALING => [self::STATUS_ACTIVE, self::STATUS_CANCELED, self::STATUS_EXPIRED],
        self::STATUS_ACTIVE => [self::STATUS_PAST_DUE, self::STATUS_CANCELED, self::STATUS_EXPIRED],
        self::STATUS_PAST_DUE => [self::STATUS_ACTIVE, self::STATUS_CANCELED, self::STATUS_EXPIRED],
        self::STATUS_CANCELED => [],
        self::STATUS_EXPIRED => [],
    ];

    protected $fillable = [
        'user_id', 'plan_id', 'status', 'trial_ends_at',
        'starts_at', 'expires_at', 'canceled_at',
        'gateway', 'gateway_subscription_id',
    ];

    protected $casts = [
        'trial_ends_at' => 'datetime',
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
        'canceled_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public function payments(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function isActive(): bool
    {
        return in_array($this->status, [self::STATUS_ACTIVE, self::STATUS_TRIALING, self::STATUS_PAST_DUE], true);
    }

    public function isTrialing(): bool
    {
        return $this->status === self::STATUS_TRIALING && $this->trial_ends_at?->isFuture();
    }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    // Scopes
    public function scopeActive(Builder $q): Builder
    {
        return $q->whereIn('status', [self::STATUS_ACTIVE, self::STATUS_TRIALING, self::STATUS_PAST_DUE]);
    }

    public function canTransitionTo(string $nextStatus): bool
    {
        return in_array($nextStatus, self::ALLOWED_TRANSITIONS[$this->status] ?? [], true);
    }
}
