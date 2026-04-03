<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('usage_aggregates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('plan_id')->nullable()->constrained()->nullOnDelete();
            $table->string('feature', 100);
            $table->string('billing_period', 20);
            $table->unsignedBigInteger('total_quantity')->default(0);
            $table->json('metadata')->nullable();
            $table->timestamp('aggregated_at');
            $table->timestamps();

            $table->unique(['user_id', 'feature', 'billing_period'], 'uniq_usage_aggregate_period');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('usage_aggregates');
    }
};
