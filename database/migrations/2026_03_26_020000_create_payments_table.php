<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_subscription_id')->constrained()->cascadeOnDelete();
            $table->string('gateway', 30)->comment('stripe, pagseguro, manual');
            $table->string('gateway_payment_id')->nullable()->index();
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('BRL');
            $table->enum('status', ['pending', 'paid', 'failed', 'refunded'])->default('pending');
            $table->json('gateway_response')->nullable();
            $table->dateTime('paid_at')->nullable();
            $table->timestamps();
        });

        // Adicionar colunas de gateway na user_subscriptions (se não existirem)
        if (! Schema::hasColumn('user_subscriptions', 'gateway')) {
            Schema::table('user_subscriptions', function (Blueprint $table) {
                $table->string('gateway', 30)->nullable()->after('canceled_at');
                $table->string('gateway_subscription_id')->nullable()->after('gateway');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');

        if (Schema::hasColumn('user_subscriptions', 'gateway')) {
            Schema::table('user_subscriptions', function (Blueprint $table) {
                $table->dropColumn(['gateway', 'gateway_subscription_id']);
            });
        }
    }
};
