<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationPreference extends Model
{
    protected $fillable = [
        'user_id', 'type', 'channel', 'enabled', 'threshold',
    ];

    protected $casts = [
        'enabled' => 'boolean',
        'threshold' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if a notification type is enabled for the user.
     */
    public static function isEnabled(int $userId, string $type, string $channel = 'dashboard'): bool
    {
        $preference = static::where('user_id', $userId)
            ->where('type', $type)
            ->where('channel', $channel)
            ->first();

        return $preference ? $preference->enabled : true; // enabled by default
    }

    /**
     * Get the threshold for a notification type.
     */
    public static function getThreshold(int $userId, string $type): ?float
    {
        $preference = static::where('user_id', $userId)
            ->where('type', $type)
            ->first();

        return $preference?->threshold;
    }
}
