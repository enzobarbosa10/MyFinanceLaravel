<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 50)->unique();
            $table->string('name', 100);
            $table->decimal('price', 10, 2)->default(0);
            $table->enum('billing_cycle', ['monthly', 'yearly', 'lifetime'])->default('monthly');
            $table->unsignedInteger('trial_days')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('plan_features', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plan_id')->constrained()->cascadeOnDelete();
            $table->string('feature', 100);
            $table->integer('limit_value')->nullable()->comment('NULL=ilimitado, 0=desabilitado, N=limite');
            $table->unique(['plan_id', 'feature']);
        });

        Schema::create('user_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('plan_id')->constrained()->restrictOnDelete();
            $table->enum('status', ['active', 'trialing', 'canceled', 'expired'])->default('active');
            $table->dateTime('trial_ends_at')->nullable();
            $table->dateTime('starts_at');
            $table->dateTime('expires_at')->nullable();
            $table->dateTime('canceled_at')->nullable();
            $table->timestamps();

            $table->index('status');
        });

        Schema::create('subscription_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('from_plan_id')->nullable();
            $table->foreignId('to_plan_id')->constrained('plans');
            $table->enum('action', ['subscribe', 'upgrade', 'downgrade', 'cancel', 'expire', 'reactivate']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscription_history');
        Schema::dropIfExists('user_subscriptions');
        Schema::dropIfExists('plan_features');
        Schema::dropIfExists('plans');
    }
};
