<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->index(['user_id', 'transaction_at'], 'txn_user_date_idx');
            $table->index(['user_id', 'type'], 'txn_user_type_idx');
            $table->index(['user_id', 'category_id'], 'txn_user_category_idx');
            $table->index(['user_id', 'account_id'], 'txn_user_account_idx');
        });

        Schema::table('accounts', function (Blueprint $table) {
            $table->index(['user_id', 'type'], 'acct_user_type_idx');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->index(['user_id', 'type'], 'cat_user_type_idx');
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropIndex('txn_user_date_idx');
            $table->dropIndex('txn_user_type_idx');
            $table->dropIndex('txn_user_category_idx');
            $table->dropIndex('txn_user_account_idx');
        });

        Schema::table('accounts', function (Blueprint $table) {
            $table->dropIndex('acct_user_type_idx');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->dropIndex('cat_user_type_idx');
        });
    }
};
