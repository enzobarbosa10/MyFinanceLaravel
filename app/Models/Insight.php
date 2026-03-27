<?php

namespace App\Models;

use App\Enums\InsightType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Insight extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id', 'type', 'title', 'message', 'impact_value',
        'is_read', 'read_at', 'expires_at', 'related_type', 'related_id',
    ];

    protected $casts = [
        'type' => InsightType::class,
        'impact_value' => 'decimal:2',
        'is_read' => 'boolean',
        'read_at' => 'datetime',
        'expires_at' => 'datetime',
        'created_at' => 'datetime',
    ];

    // ── Relationships ────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function related(): MorphTo
    {
        return $this->morphTo();
    }

    // ── Scopes ───────────────────────────────────────────────

    public function scopeUnread(Builder $query): Builder
    {
        return $query->where('is_read', false);
    }

    public function scopeRead(Builder $query): Builder
    {
        return $query->where('is_read', true);
    }

    public function scopeAlerts(Builder $query): Builder
    {
        return $query->where('type', InsightType::Alert);
    }

    public function scopeSuggestions(Builder $query): Builder
    {
        return $query->where('type', InsightType::Suggestion);
    }

    public function scopeRisks(Builder $query): Builder
    {
        return $query->where('type', InsightType::Risk);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where(function (Builder $q) {
            $q->whereNull('expires_at')
              ->orWhere('expires_at', '>', now());
        });
    }

    public function scopeExpired(Builder $query): Builder
    {
        return $query->whereNotNull('expires_at')
                     ->where('expires_at', '<=', now());
    }

    // ── Methods ──────────────────────────────────────────────

    public function markAsRead(): void
    {
        $this->update([
            'is_read' => true,
            'read_at' => now(),
        ]);
    }
}
