<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InvestmentAsset extends Model
{
    public $timestamps = false;
    protected $fillable = ['name', 'symbol', 'type_id'];

    public function type(): BelongsTo
    {
        return $this->belongsTo(InvestmentType::class, 'type_id');
    }

    public function investments(): HasMany
    {
        return $this->hasMany(Investment::class, 'asset_id');
    }
}
