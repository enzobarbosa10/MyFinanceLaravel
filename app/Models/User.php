<?php

namespace App\Models;

use App\Models\Concerns\HasFinancialRelationships;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, HasFinancialRelationships, Notifiable;

    protected $fillable = ['name', 'email', 'password', 'had_trial', 'is_admin'];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'had_trial' => 'boolean',
            'is_admin' => 'boolean',
        ];
    }

    public function currentPlan(): ?Plan
    {
        return $this->userSubscription?->plan;
    }

    public function onPlan(string $slug): bool
    {
        return $this->currentPlan()?->slug === $slug;
    }

    public function onPlanOrHigher(string $slug): bool
    {
        $hierarchy = ['free' => 0, 'pro' => 1, 'premium' => 2];
        $current = $hierarchy[$this->currentPlan()?->slug ?? 'free'] ?? 0;
        return $current >= ($hierarchy[$slug] ?? 0);
    }

    public function canUseFeature(string $feature): bool
    {
        $plan = $this->currentPlan();
        if (! $plan) {
            $plan = Plan::free();
        }
        return $plan ? $plan->hasFeature($feature) : false;
    }

    public function featureLimit(string $feature): ?int
    {
        $plan = $this->currentPlan() ?? Plan::free();
        return $plan?->featureLimit($feature);
    }
}
