<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('accounts', function (Blueprint $table) {
            $table->string('open_finance_id')->nullable()->unique()->after('type');
            $table->string('open_finance_item_id')->nullable()->index()->after('open_finance_id');
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->string('open_finance_id')->nullable()->unique()->after('category_id');
        });
    }

    public function down(): void
    {
        Schema::table('accounts', function (Blueprint $table) {
            $table->dropColumn(['open_finance_id', 'open_finance_item_id']);
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn('open_finance_id');
        });
    }
};
