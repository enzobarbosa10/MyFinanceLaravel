<?php

namespace App\Models\Concerns;

use App\Models\Account;
use App\Models\Budget;
use App\Models\CategorizationRule;
use App\Models\Category;
use App\Models\Debt;
use App\Models\FeatureUsage;
use App\Models\Goal;
use App\Models\Insight;
use App\Models\Installment;
use App\Models\Investment;
use App\Models\Notification;
use App\Models\NotificationPreference;
use App\Models\Subscription;
use App\Models\Transaction;
use App\Models\UserSubscription;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

trait HasFinancialRelationships
{
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
}
