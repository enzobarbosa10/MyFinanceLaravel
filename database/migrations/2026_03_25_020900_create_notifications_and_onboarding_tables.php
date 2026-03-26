<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('type', 50);
            $table->enum('severity', ['info', 'warning', 'critical', 'positive'])->default('info');
            $table->string('title', 255);
            $table->text('message');
            $table->json('data')->nullable();
            $table->string('channel', 30)->default('dashboard');
            $table->dateTime('read_at')->nullable();
            $table->boolean('dismissed')->default(false);
            $table->timestamps();

            $table->index(['user_id', 'read_at']);
            $table->index(['user_id', 'type']);
            $table->index('channel');
        });

        Schema::create('notification_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('type', 50);
            $table->string('channel', 30)->default('dashboard');
            $table->boolean('enabled')->default(true);
            $table->decimal('threshold', 10, 2)->nullable();
            $table->timestamps();
            $table->unique(['user_id', 'type', 'channel']);
        });

        Schema::create('user_onboarding', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->tinyInteger('current_step')->default(1);
            $table->string('financial_goal', 50)->nullable();
            $table->boolean('categories_configured')->default(false);
            $table->boolean('data_imported')->default(false);
            $table->boolean('completed')->default(false);
            $table->dateTime('completed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_onboarding');
        Schema::dropIfExists('notification_preferences');
        Schema::dropIfExists('notifications');
    }
};
