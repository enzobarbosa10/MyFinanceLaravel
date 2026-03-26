<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('budgets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 12, 2);
            $table->char('month', 7)->comment('Formato: YYYY-MM');
            $table->timestamps();
            $table->unique(['user_id', 'category_id', 'month']);
        });

        Schema::create('budget_alerts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('budget_id')->constrained()->cascadeOnDelete();
            $table->enum('alert_type', ['warning', 'exceeded']);
            $table->char('month', 7);
            $table->decimal('percentage', 5, 1);
            $table->boolean('seen')->default(false);
            $table->timestamps();
            $table->unique(['budget_id', 'alert_type', 'month']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('budget_alerts');
        Schema::dropIfExists('budgets');
    }
};
