<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Debt extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id', 'name', 'total_amount', 'paid_amount',
        'monthly_interest_rate', 'min_payment', 'due_day',
        'creditor', 'status',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'monthly_interest_rate' => 'decimal:4',
        'min_payment' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(DebtPayment::class);
    }

    public function remainingBalance(): float
    {
        return max(0, $this->total_amount - $this->paid_amount);
    }
}
