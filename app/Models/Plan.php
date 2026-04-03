<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
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

    public function userSubscriptions(): HasMany
    {
        return $this->hasMany(UserSubscription::class);
    }

    public function featureUsages(): HasMany
    {
        return $this->hasMany(FeatureUsage::class);
    }

    public function hasFeature(string $feature): bool
    {
        $pf = $this->features()->where('feature', $feature)->first();
        return $pf && $pf->limit_value !== 0;
    }

    /**
     * NULL = ilimitado, 0 = desabilitado, N = limite numérico.
     */
    public function featureLimit(string $feature): ?int
    {
        return $this->features()->where('feature', $feature)->value('limit_value');
    }

    // Scopes
    public function scopeActive(Builder $q): Builder { return $q->where('is_active', true); }
    public function scopeBySlug(Builder $q, string $slug): Builder { return $q->where('slug', $slug); }

    public static function free(): ?self  { return static::where('slug', 'free')->first(); }
    public static function pro(): ?self   { return static::where('slug', 'pro')->first(); }
    public static function premium(): ?self { return static::where('slug', 'premium')->first(); }
}
