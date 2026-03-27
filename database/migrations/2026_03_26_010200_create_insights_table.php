<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('insights', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['alert', 'suggestion', 'risk']);
            $table->string('title', 255);
            $table->text('message');
            $table->decimal('impact_value', 12, 2)->nullable();
            $table->boolean('is_read')->default(false);
            $table->dateTime('read_at')->nullable();
            $table->dateTime('expires_at')->nullable();
            $table->nullableMorphs('related');
            $table->timestamp('created_at')->useCurrent();

            $table->index(['user_id', 'type']);
            $table->index(['user_id', 'is_read']);
            $table->index(['user_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('insights');
    }
};
