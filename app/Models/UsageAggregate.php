<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UsageAggregate extends Model
{
    protected $fillable = [
        'user_id',
        'plan_id',
        'feature',
        'billing_period',
        'total_quantity',
        'metadata',
        'aggregated_at',
    ];

    protected $casts = [
        'metadata' => 'array',
        'aggregated_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }
}
