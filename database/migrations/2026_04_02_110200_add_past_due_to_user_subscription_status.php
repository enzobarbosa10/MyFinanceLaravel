<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        DB::statement("ALTER TABLE user_subscriptions MODIFY status ENUM('pending','active','trialing','past_due','canceled','expired') NOT NULL DEFAULT 'pending'");
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        DB::statement("ALTER TABLE user_subscriptions MODIFY status ENUM('pending','active','trialing','canceled','expired') NOT NULL DEFAULT 'pending'");
    }
};
