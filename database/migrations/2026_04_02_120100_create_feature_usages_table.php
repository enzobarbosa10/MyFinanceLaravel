<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('feature_usages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('plan_id')->nullable()->constrained()->nullOnDelete();
            $table->string('feature', 100);
            $table->string('period', 20);
            $table->unsignedBigInteger('usage_count')->default(0);
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'feature', 'period'], 'uniq_feature_usage_period');
            $table->index(['feature', 'period']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feature_usages');
    }
};
