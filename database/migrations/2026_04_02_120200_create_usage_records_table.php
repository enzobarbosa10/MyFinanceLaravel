<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('usage_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('plan_id')->nullable()->constrained()->nullOnDelete();
            $table->string('feature', 100);
            $table->unsignedInteger('quantity')->default(1);
            $table->string('billing_period', 20);
            $table->boolean('is_aggregated')->default(false);
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['feature', 'billing_period', 'is_aggregated']);
            $table->index(['user_id', 'billing_period']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('usage_records');
    }
};
