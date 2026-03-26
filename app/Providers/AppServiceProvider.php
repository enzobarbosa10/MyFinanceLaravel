<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(\App\Models\Goal::class, \App\Policies\GoalPolicy::class);
        Gate::policy(\App\Models\Debt::class, \App\Policies\DebtPolicy::class);
        Gate::policy(\App\Models\Transaction::class, \App\Policies\TransactionPolicy::class);
    }
}
