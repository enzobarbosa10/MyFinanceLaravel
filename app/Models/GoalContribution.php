<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GoalContribution extends Model
{
    protected $fillable = ['goal_id', 'amount', 'contributed_at', 'notes'];

    protected $casts = [
        'amount' => 'decimal:2',
        'contributed_at' => 'date',
    ];

    public function goal(): BelongsTo
    {
        return $this->belongsTo(Goal::class);
    }
}
