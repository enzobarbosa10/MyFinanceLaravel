<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('account_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->string('type', 10); // 'entrada' ou 'saida'
            $table->decimal('amount', 15, 2);
            $table->string('description', 255)->nullable();
            $table->dateTime('transaction_at');
            $table->string('source', 20)->default('manual');
            $table->text('raw_description')->nullable();
            $table->float('confidence_score')->nullable();
            $table->unsignedBigInteger('import_id')->nullable();
            $table->boolean('is_recurring')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
