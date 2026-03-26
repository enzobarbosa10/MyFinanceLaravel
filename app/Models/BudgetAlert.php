<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BudgetAlert extends Model
{
    protected $fillable = ['user_id', 'budget_id', 'alert_type', 'month', 'percentage', 'seen'];

    protected $casts = [
        'percentage' => 'decimal:1',
        'seen' => 'boolean',
    ];

    public function budget(): BelongsTo
    {
        return $this->belongsTo(Budget::class);
    }
}
