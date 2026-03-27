<?php

namespace Database\Seeders;

use App\Models\Plan;
use App\Models\PlanFeature;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            [
                'slug' => 'free',
                'name' => 'Free',
                'price' => 0,
                'billing_cycle' => 'monthly',
                'trial_days' => 0,
                'features' => [
                    'accounts' => 2,
                    'transactions_per_month' => 100,
                    'categories' => 10,
                    'budgets' => 2,
                    'goals' => 1,
                    'debts' => 2,
                    'investments' => 0,       // desabilitado
                    'installments' => 3,
                    'subscriptions' => 5,
                    'ai_assistant' => 0,      // desabilitado
                    'insights' => 3,
                    'import_csv' => 0,        // desabilitado
                    'notifications' => 5,
                    'reports_export' => 0,    // desabilitado
                    'contacts' => 5,
                ],
            ],
            [
                'slug' => 'pro',
                'name' => 'Pro',
                'price' => 19.90,
                'billing_cycle' => 'monthly',
                'trial_days' => 7,
                'features' => [
                    'accounts' => 10,
                    'transactions_per_month' => null, // ilimitado
                    'categories' => null,
                    'budgets' => 10,
                    'goals' => 10,
                    'debts' => 10,
                    'investments' => 10,
                    'installments' => null,
                    'subscriptions' => null,
                    'ai_assistant' => 20,    // 20 perguntas/mês
                    'insights' => null,
                    'import_csv' => 1,       // habilitado
                    'notifications' => null,
                    'reports_export' => 1,   // habilitado
                    'contacts' => 50,
                ],
            ],
            [
                'slug' => 'premium',
                'name' => 'Premium',
                'price' => 39.90,
                'billing_cycle' => 'monthly',
                'trial_days' => 7,
                'features' => [
                    'accounts' => null,      // ilimitado
                    'transactions_per_month' => null,
                    'categories' => null,
                    'budgets' => null,
                    'goals' => null,
                    'debts' => null,
                    'investments' => null,
                    'installments' => null,
                    'subscriptions' => null,
                    'ai_assistant' => null,   // ilimitado
                    'insights' => null,
                    'import_csv' => 1,
                    'notifications' => null,
                    'reports_export' => 1,
                    'contacts' => null,
                ],
            ],
        ];

        foreach ($plans as $planData) {
            $features = $planData['features'];
            unset($planData['features']);

            $plan = Plan::updateOrCreate(
                ['slug' => $planData['slug']],
                $planData
            );

            foreach ($features as $feature => $limit) {
                PlanFeature::updateOrCreate(
                    ['plan_id' => $plan->id, 'feature' => $feature],
                    ['limit_value' => $limit]
                );
            }
        }
    }
}
