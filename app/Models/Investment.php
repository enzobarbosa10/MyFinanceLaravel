<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Investment extends Model
{
    protected $fillable = ['user_id', 'asset_id', 'quantity', 'purchase_price'];

    protected $casts = [
        'quantity' => 'decimal:4',
        'purchase_price' => 'decimal:4',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function asset(): BelongsTo
    {
        return $this->belongsTo(InvestmentAsset::class, 'asset_id');
    }

    public function totalValue(): float
    {
        return $this->quantity * $this->purchase_price;
    }
}
