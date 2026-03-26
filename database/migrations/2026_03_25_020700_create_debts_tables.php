<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('debts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name', 100);
            $table->decimal('total_amount', 12, 2);
            $table->decimal('paid_amount', 12, 2)->default(0);
            $table->decimal('monthly_interest_rate', 5, 4)->default(0);
            $table->decimal('min_payment', 12, 2)->default(0);
            $table->tinyInteger('due_day')->default(1);
            $table->string('creditor', 100)->nullable();
            $table->enum('status', ['active', 'paid'])->default('active');
            $table->timestamps();

            $table->index(['user_id', 'status']);
        });

        Schema::create('debt_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('debt_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 12, 2);
            $table->date('paid_at');
            $table->string('notes', 255)->nullable();
            $table->timestamps();

            $table->index(['debt_id', 'paid_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('debt_payments');
        Schema::dropIfExists('debts');
    }
};
