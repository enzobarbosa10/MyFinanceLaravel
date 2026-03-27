<?php

namespace App\Providers;

use App\Events\GoalBehindSchedule;
use App\Events\LowBalanceDetected;
use App\Events\UnusualSpendingDetected;
use App\Listeners\SendGoalBehindScheduleNotification;
use App\Listeners\SendLowBalanceNotification;
use App\Listeners\SendUnusualSpendingNotification;
use App\Services\InsightEngine\InsightEngine;
use App\Services\InsightEngine\Rules\CategoryDominanceRule;
use App\Services\InsightEngine\Rules\NegativeBalanceRule;
use App\Services\InsightEngine\Rules\SpendingIncreaseRule;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(InsightEngine::class, function () {
            return (new InsightEngine())->addRules([
                new SpendingIncreaseRule(),
                new CategoryDominanceRule(),
                new NegativeBalanceRule(),
            ]);
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(\App\Models\Goal::class, \App\Policies\GoalPolicy::class);
        Gate::policy(\App\Models\Debt::class, \App\Policies\DebtPolicy::class);
        Gate::policy(\App\Models\Transaction::class, \App\Policies\TransactionPolicy::class);

        // Financial Notification Events → Listeners
        Event::listen(UnusualSpendingDetected::class, SendUnusualSpendingNotification::class);
        Event::listen(LowBalanceDetected::class, SendLowBalanceNotification::class);
        Event::listen(GoalBehindSchedule::class, SendGoalBehindScheduleNotification::class);
    }
}
