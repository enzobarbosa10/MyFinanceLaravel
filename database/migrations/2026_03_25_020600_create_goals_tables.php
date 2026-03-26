<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('goals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name', 100);
            $table->decimal('target_amount', 12, 2);
            $table->decimal('current_amount', 12, 2)->default(0);
            $table->date('deadline');
            $table->string('icon', 10)->default('🎯');
            $table->enum('status', ['active', 'completed', 'cancelled'])->default('active');
            $table->timestamps();

            $table->index(['user_id', 'status']);
        });

        Schema::create('goal_contributions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('goal_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 12, 2);
            $table->date('contributed_at');
            $table->string('notes', 255)->nullable();
            $table->timestamps();

            $table->index(['goal_id', 'contributed_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('goal_contributions');
        Schema::dropIfExists('goals');
    }
};
