<?php

namespace App\Models;

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
    use HasApiTokens, HasFactory, Notifiable;

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

    public function accounts(): HasMany
    {
        return $this->hasMany(Account::class);
    }

    public function categories(): HasMany
    {
        return $this->hasMany(Category::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function budgets(): HasMany
    {
        return $this->hasMany(Budget::class);
    }

    public function goals(): HasMany
    {
        return $this->hasMany(Goal::class);
    }

    public function debts(): HasMany
    {
        return $this->hasMany(Debt::class);
    }

    public function investments(): HasMany
    {
        return $this->hasMany(Investment::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function installments(): HasMany
    {
        return $this->hasMany(Installment::class);
    }

    public function insights(): HasMany
    {
        return $this->hasMany(Insight::class);
    }

    public function categorizationRules(): HasMany
    {
        return $this->hasMany(CategorizationRule::class);
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    public function featureUsages(): HasMany
    {
        return $this->hasMany(FeatureUsage::class);
    }

    public function notificationPreferences(): HasMany
    {
        return $this->hasMany(NotificationPreference::class);
    }

    public function userSubscription(): HasOne
    {
        return $this->hasOne(UserSubscription::class)->whereIn('status', ['active', 'trialing'])->latest();
    }

    public function currentOpenSubscription(): HasOne
    {
        return $this->hasOne(UserSubscription::class)
            ->whereIn('status', UserSubscription::OPEN_STATUSES)
            ->latest();
    }

    public function subscriptionHistory(): HasMany
    {
        return $this->hasMany(UserSubscription::class)->latest();
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
