<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\FinancialNotificationService;
use Illuminate\Console\Command;

class CheckFinancialNotifications extends Command
{
    protected $signature = 'notifications:check-financial';

    protected $description = 'Check for behind-schedule goals and low balances across all users';

    public function handle(FinancialNotificationService $service): int
    {
        $users = User::has('goals')->orHas('accounts')->get();
        $count = 0;

        foreach ($users as $user) {
            $service->checkGoalsBehindSchedule($user);

            foreach ($user->accounts as $account) {
                $service->checkLowBalance($account);
            }

            $count++;
        }

        $this->info("Checked {$count} users for financial notifications.");

        return self::SUCCESS;
    }
}
