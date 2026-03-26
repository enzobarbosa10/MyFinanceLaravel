<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('investment_types', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50);
        });

        Schema::create('investment_assets', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('symbol', 20);
            $table->foreignId('type_id')->constrained('investment_types')->cascadeOnDelete();
        });

        Schema::create('investments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('asset_id')->constrained('investment_assets')->cascadeOnDelete();
            $table->decimal('quantity', 18, 4);
            $table->decimal('purchase_price', 18, 4);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('investments');
        Schema::dropIfExists('investment_assets');
        Schema::dropIfExists('investment_types');
    }
};
