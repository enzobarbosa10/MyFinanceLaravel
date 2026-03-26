<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Plan extends Model
{
    protected $fillable = ['slug', 'name', 'price', 'billing_cycle', 'trial_days', 'is_active'];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function features(): HasMany
    {
        return $this->hasMany(PlanFeature::class);
    }

    public function hasFeature(string $feature): bool
    {
        $pf = $this->features()->where('feature', $feature)->first();
        return $pf && $pf->limit_value !== 0;
    }
}
