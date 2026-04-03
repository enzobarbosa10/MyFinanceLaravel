<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            Schema::table('user_subscriptions', function (Blueprint $table) {
                $table->unsignedBigInteger('open_subscription_user_id')
                    ->nullable()
                    ->storedAs("case when status in ('pending','active','trialing','past_due') then user_id else null end")
                    ->after('user_id');
            });

            Schema::table('user_subscriptions', function (Blueprint $table) {
                $table->unique('open_subscription_user_id', 'uniq_open_subscription_user');
            });

            return;
        }

        DB::statement("CREATE UNIQUE INDEX uniq_open_subscription_user ON user_subscriptions(user_id) WHERE status IN ('pending','active','trialing','past_due')");
    }

    public function down(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            Schema::table('user_subscriptions', function (Blueprint $table) {
                $table->dropUnique('uniq_open_subscription_user');
                $table->dropColumn('open_subscription_user_id');
            });

            return;
        }

        DB::statement('DROP INDEX IF EXISTS uniq_open_subscription_user');
    }
};
