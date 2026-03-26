<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('imports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('file_name', 255);
            $table->string('status', 50)->default('pending');
            $table->integer('total_rows')->default(0);
            $table->integer('imported_rows')->default(0);
            $table->timestamps();
        });

        // Add FK that was deferred from transactions migration
        Schema::table('transactions', function (Blueprint $table) {
            $table->foreign('import_id')->references('id')->on('imports')->nullOnDelete();
        });

        Schema::create('variable_income_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->boolean('is_active')->default(false);
            $table->integer('months_average')->default(6);
            $table->integer('reserve_months')->default(3);
            $table->timestamps();
        });

        Schema::create('ai_usage', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('usage_date');
            $table->integer('message_count')->default(0);
            $table->timestamps();
            $table->unique(['user_id', 'usage_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_usage');
        Schema::dropIfExists('variable_income_settings');
        Schema::dropIfExists('imports');
    }
};
